<?php

namespace App\Http\Controllers\Pedagang;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Setting;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedagangController extends Controller
{
    private function getTarif(): array
    {
        $settings = Setting::whereIn('nama_setting', ['harian_rate','mingguan_rate','bulanan_rate'])->pluck('nilai','nama_setting');
        return [
            'harian'   => (int)($settings['harian_rate']   ?? 5000),
            'mingguan' => (int)($settings['mingguan_rate'] ?? 35000),
            'bulanan'  => (int)($settings['bulanan_rate']  ?? 150000),
        ];
    }

    public function dashboard()
    {
        $user    = Auth::user();
        $id_user = $user->id_user;

        // Ambil notifikasi - TIDAK auto mark as read di sini
        $notifikasi = Notifikasi::where('id_user', $id_user)
            ->orderByDesc('created_at')->limit(10)->get();
        $unread = Notifikasi::where('id_user', $id_user)->where('dibaca', false)->count();

        $lastTrx = Transaksi::where('id_user', $id_user)->where('status', 'approved')
            ->orderByDesc('tanggal')->first();

        $jatuhTempoStr = null; $sisaHari = null; $sudahLewat = false;
        $jatuhTempoDatetime = null;

        if ($lastTrx) {
            $intervalMap = ['Harian' => 1, 'Mingguan' => 7, 'Bulanan' => 30];
            $hari        = $intervalMap[$lastTrx->jenis_pajak] ?? 30;
            // Jatuh tempo = tanggal bayar + interval, sampai akhir hari (23:59:59)
            $jt          = $lastTrx->tanggal->copy()->addDays($hari)->endOfDay();
            $now         = now();
            $sudahLewat  = $jt->lt($now);
            $sisaDetik   = $sudahLewat ? 0 : $now->diffInSeconds($jt);
            // Countdown aktif jika sisa waktu <= 24 jam (86400 detik)
            $showCountdown = !$sudahLewat && $sisaDetik <= 86400;
            $sisaHari    = $sudahLewat ? abs((int)$jt->diffInDays($now)) : (int)$now->diffInDays($jt);
            $jatuhTempoStr = $jt->locale('id')->isoFormat('D MMM YYYY');
            $jatuhTempoDatetime = $jt->format('Y-m-d H:i:s'); // format untuk JS
        }

        $transaksiTerbaru = Transaksi::where('id_user', $id_user)->orderByDesc('tanggal')->limit(5)->get();
        $totalBulanIni    = Transaksi::where('id_user', $id_user)->where('status','approved')
            ->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('nominal');

        $beritaList = \App\Models\Berita::where('status', 'aktif')->orderByDesc('created_at')->limit(5)->get();
        $tarif      = $this->getTarif();

        $jatuhTempoDatetime = $jatuhTempoDatetime ?? null;
        $showCountdown      = $showCountdown ?? false;

        return view('pedagang.dashboard', compact(
            'user','notifikasi','unread','lastTrx',
            'jatuhTempoStr','jatuhTempoDatetime','sisaHari','sudahLewat','showCountdown',
            'transaksiTerbaru','totalBulanIni','beritaList','tarif'
        ));
    }

    // Mark notifikasi as read saat bell diklik (AJAX)
    public function markNotifRead()
    {
        Notifikasi::where('id_user', Auth::id())->where('dibaca', false)->update(['dibaca' => true]);
        return response()->json(['success' => true]);
    }

    public function pembayaran()
    {
        $user       = Auth::user();
        $dataSukses = null;
        $tarif      = $this->getTarif();
        return view('pedagang.pembayaran', compact('user','dataSukses','tarif'));
    }

    public function prosesBayar(Request $request)
    {
        $user  = Auth::user();
        $tarif = $this->getTarif();

        $request->validate([
            'jenis_pajak' => 'required|in:Harian,Mingguan,Bulanan',
            'metode'      => 'required|string',
            'nominal'     => 'required|numeric',
        ]);

        $nominalMap   = ['Harian'=>$tarif['harian'],'Mingguan'=>$tarif['mingguan'],'Bulanan'=>$tarif['bulanan']];
        $nominalBenar = $nominalMap[$request->jenis_pajak];
        $noTrx        = 'TRX-'.now()->format('Ymd').'-'.rand(100000,999999);
        $tanggal      = now();

        DB::transaction(function () use ($user, $noTrx, $tanggal, $request, $nominalBenar) {
            Transaksi::create([
                'id_user'           => $user->id_user,
                'no_trx'            => $noTrx,
                'nomor_kios'        => $user->no_kios,
                'jenis_pajak'       => $request->jenis_pajak,
                'metode_pembayaran' => $request->metode,
                'nominal'           => $nominalBenar,
                'tanggal'           => $tanggal,
                'status'            => 'pending',
            ]);
        });

        $dataSukses = [
            'no_trx'  => $noTrx,
            'kios'    => $user->no_kios,
            'jenis'   => $request->jenis_pajak,
            'metode'  => $request->metode,
            'nominal' => $nominalBenar,
            'tanggal' => $tanggal->locale('id')->isoFormat('D MMMM YYYY'),
        ];

        return view('pedagang.pembayaran', compact('user','dataSukses','tarif'));
    }

    public function riwayat()
    {
        $user = Auth::user();
        $transaksi = Transaksi::where('id_user', $user->id_user)
            ->orderByDesc('id_transaksi')->get()
            ->map(fn($t) => [
                'id'      => $t->no_trx,
                'kios'    => $t->nomor_kios,
                'jenis'   => $t->jenis_pajak,
                'metode'  => $t->metode_pembayaran,
                'jumlah'  => (int)$t->nominal,
                'tanggal' => $t->tanggal->format('Y-m-d H:i:s'),
                'status'  => $t->status,
                'catatan_verifikasi' => $t->catatan_verifikasi,
            ]);
        return view('pedagang.riwayat', compact('user','transaksi'));
    }
}