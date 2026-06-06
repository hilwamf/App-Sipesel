<?php

namespace App\Http\Controllers\Pedagang;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedagangController extends Controller
{
    public function dashboard()
    {
        $user    = Auth::user();
        $id_user = $user->id_user;

        $notifikasi = Notifikasi::where('id_user', $id_user)
            ->orderByDesc('created_at')->limit(10)->get();
        $unread = $notifikasi->where('dibaca', false)->count();

        if ($unread > 0) {
            Notifikasi::where('id_user', $id_user)->where('dibaca', false)->update(['dibaca' => true]);
        }

        $lastTrx = Transaksi::where('id_user', $id_user)->where('status', 'approved')
            ->orderByDesc('tanggal')->first();

        $jatuhTempoStr = null; $sisaHari = null; $sudahLewat = false;

        if ($lastTrx) {
            $intervalMap = ['Harian' => 1, 'Mingguan' => 7, 'Bulanan' => 30];
            $hari   = $intervalMap[$lastTrx->jenis_pajak] ?? 30;
            $jt     = $lastTrx->tanggal->copy()->addDays($hari);
            $now    = now();
            $sudahLewat    = $jt->lt($now);
            $sisaHari      = abs((int)$jt->diffInDays($now));
            $jatuhTempoStr = $jt->locale('id')->isoFormat('D MMM YYYY');
        }

        return view('pedagang.dashboard', compact('user','notifikasi','unread','lastTrx','jatuhTempoStr','sisaHari','sudahLewat'));
    }

    public function pembayaran()
    {
        $user = Auth::user();
        $dataSukses = null;
        return view('pedagang.pembayaran', compact('user','dataSukses'));
    }

    public function prosesBayar(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'jenis_pajak' => 'required|in:Harian,Mingguan,Bulanan',
            'metode'      => 'required|string',
            'nominal'     => 'required|numeric',
        ]);

        $noTrx   = 'TRX-'.now()->format('Ymd').'-'.rand(100000,999999);
        $tanggal = now();

        Transaksi::create([
            'id_user'           => $user->id_user,
            'no_trx'            => $noTrx,
            'nomor_kios'        => $user->no_kios,
            'jenis_pajak'       => $request->jenis_pajak,
            'metode_pembayaran' => $request->metode,
            'nominal'           => $request->nominal,
            'tanggal'           => $tanggal,
            'status'            => 'pending',
        ]);

        $dataSukses = [
            'no_trx'  => $noTrx,
            'kios'    => $user->no_kios,
            'jenis'   => $request->jenis_pajak,
            'metode'  => $request->metode,
            'nominal' => $request->nominal,
            'tanggal' => $tanggal->locale('id')->isoFormat('D MMMM YYYY'),
        ];

        return view('pedagang.pembayaran', compact('user','dataSukses'));
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
