<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengawasController extends Controller
{
    public function dashboard()
    {
        $user          = Auth::user();
        $totalPedagang = User::where('role', 'pedagang')->count();
        $totalPending  = Transaksi::where('status', 'pending')->count();

        // Hitung pedagang sudah bayar bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $sudahBayar = User::where('role', 'pedagang')
            ->whereHas('transaksi', fn($q) => $q->where('status', 'approved')
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni))
            ->count();
        $kepatuhan = $totalPedagang > 0 ? round($sudahBayar / $totalPedagang * 100) : 0;

        // Hitung pedagang jatuh tempo
        $sekarang = now();
        $jatuhTempo = 0;
        $pedagang = User::where('role', 'pedagang')->with(['transaksi' => fn($q) => $q->where('status', 'approved')->orderByDesc('tanggal')])->get();
        foreach ($pedagang as $p) {
            $lastTrx = $p->transaksi->first();
            if (!$lastTrx) { $jatuhTempo++; continue; }
            $intervalMap = ['Harian' => 1, 'Mingguan' => 7, 'Bulanan' => 30];
            $hari = $intervalMap[$lastTrx->jenis_pajak] ?? 30;
            $jt = $lastTrx->tanggal->copy()->addDays($hari);
            if ($jt->lt($sekarang)) $jatuhTempo++;
        }

        // Chart kepatuhan 6 bulan terakhir
        $chartKepatuhan = [];
        for ($i = 5; $i >= 0; $i--) {
            $d     = now()->subMonths($i);
            $total = User::where('role','pedagang')->count();
            $bayar = User::where('role','pedagang')
                ->whereHas('transaksi', fn($q) => $q->where('status','approved')
                    ->whereMonth('tanggal',$d->month)->whereYear('tanggal',$d->year))
                ->count();
            $chartKepatuhan[] = [
                'bulan'     => $d->locale('id')->isoFormat('MMM YY'),
                'kepatuhan' => $total > 0 ? round($bayar/$total*100) : 0,
                'bayar'     => $bayar,
                'belum'     => $total - $bayar,
            ];
        }

        return view('pengawas.dashboard', compact('user','totalPedagang','totalPending','sudahBayar','kepatuhan','jatuhTempo','chartKepatuhan'));
    }

    public function monitoring(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->filter ?? 'semua';

        $query = Transaksi::with('user');
        if ($filter === 'pending')  $query->where('status', 'pending');
        if ($filter === 'berhasil') $query->where('status', 'approved');
        if ($filter === 'gagal')    $query->where('status', 'rejected');

        $transaksi    = $query->orderByDesc('tanggal')->get();
        $totalNominal = $transaksi->sum('nominal');
        $rataRata     = $transaksi->count() > 0 ? $totalNominal / $transaksi->count() : 0;

        return view('pengawas.monitoring', compact('user', 'transaksi', 'filter', 'totalNominal', 'rataRata'));
    }

    public function laporan()
    {
        $user     = Auth::user();
        $sekarang = now();

        $pedagangList = User::where('role', 'pedagang')
            ->with(['transaksi' => fn($q) => $q->where('status', 'approved')->orderByDesc('tanggal')])
            ->orderBy('nama')->get()
            ->map(function ($p) use ($sekarang) {
                $lastTrx = $p->transaksi->first();
                $jatuhTempo = null; $sudahLewat = true; $selisihHari = null;

                if ($lastTrx) {
                    $intervalMap = ['Harian' => 1, 'Mingguan' => 7, 'Bulanan' => 30];
                    $hari        = $intervalMap[$lastTrx->jenis_pajak] ?? 30;
                    $jt          = $lastTrx->tanggal->copy()->addDays($hari);
                    $sudahLewat  = $jt->lt($sekarang);
                    $selisihHari = abs((int)$jt->diffInDays($sekarang));
                    $jatuhTempo  = $jt->format('Y-m-d');
                }

                return [
                    'id_user'      => $p->id_user,
                    'nama'         => $p->nama,
                    'username'     => $p->username,
                    'no_kios'      => $p->no_kios,
                    'nomor_hp'     => $p->nomor_hp,
                    'jenis_pajak'  => $lastTrx?->jenis_pajak,
                    'nominal'      => $lastTrx?->nominal,
                    'tanggal_bayar'=> $lastTrx?->tanggal?->format('Y-m-d'),
                    'jatuh_tempo'  => $jatuhTempo,
                    'sudah_lewat'  => $sudahLewat,
                    'selisih_hari' => $selisihHari,
                ];
            });

        $totalPedagang = $pedagangList->count();
        $sudahJatuh    = $pedagangList->where('sudah_lewat', true)->count();
        $belumJatuh    = $totalPedagang - $sudahJatuh;

        return view('pengawas.laporan', compact('user', 'pedagangList', 'totalPedagang', 'sudahJatuh', 'belumJatuh'));
    }

    public function kirimNotifikasi(Request $request)
    {
        $idUser = (int)$request->id_user;
        $kios   = $request->kios;

        if (!$idUser) return response()->json(['success' => false, 'message' => 'ID user tidak valid']);

        $pesan = $request->filled('pesan_custom')
            ? $request->pesan_custom
            : "Tagihan pajak kios {$kios} Anda telah melewati jatuh tempo. Segera lakukan pembayaran melalui menu Pembayaran.";

        Notifikasi::create([
            'id_user'     => $idUser,
            'id_pengirim' => Auth::id(),
            'pesan'       => $pesan,
            'dibaca'      => false,
        ]);

        return response()->json(['success' => true]);
    }
}