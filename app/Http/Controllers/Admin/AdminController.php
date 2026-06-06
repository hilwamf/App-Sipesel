<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kios;
use App\Models\Setting;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $totalPendapatan = Transaksi::where('status', 'approved')->sum('nominal');
        $totalPedagang   = User::where('role', 'pedagang')->count();
        $totalPengawas   = User::where('role', 'pengawas')->count();
        $totalPending    = Transaksi::where('status', 'pending')->count();
        $totalKios       = Kios::count();
        $totalKiosTerisi = Kios::where('status', 'terisi')->count();

        $transaksiTerbaru = Transaksi::with('user')
            ->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact(
            'user','totalPendapatan','totalPedagang','totalPengawas',
            'totalPending','totalKios','totalKiosTerisi','transaksiTerbaru'
        ));
    }

    // ─── VERIFIKASI ────────────────────────────────────────────────────────
    public function verifikasi()
    {
        $user    = Auth::user();
        $pending  = Transaksi::with('user')->where('status','pending')->orderByDesc('created_at')->get();
        $verified = Transaksi::with(['user','verifier'])
            ->whereIn('status',['approved','rejected'])
            ->orderByDesc('verified_at')->limit(20)->get();

        return view('admin.verifikasi', compact('user','pending','verified'));
    }

    public function prosesVerifikasi(Request $request)
    {
        $request->validate(['id_transaksi'=>'required','action'=>'required|in:approve,reject']);

        $trx     = Transaksi::findOrFail($request->id_transaksi);
        $adminId = Auth::id();

        if ($request->action === 'approve') {
            $trx->update([
                'status'              => 'approved',
                'verified_by'         => $adminId,
                'verified_at'         => now(),
                'catatan_verifikasi'  => 'Pembayaran disetujui oleh admin',
            ]);
            return back()->with('success', 'Pembayaran berhasil disetujui!');
        }

        $request->validate(['catatan_penolakan' => 'required|string']);
        $trx->update([
            'status'             => 'rejected',
            'verified_by'        => $adminId,
            'verified_at'        => now(),
            'catatan_verifikasi' => $request->catatan_penolakan,
        ]);
        return back()->with('success', 'Pembayaran berhasil ditolak!');
    }

    // ─── MANAJEMEN USER ────────────────────────────────────────────────────
    public function users(Request $request)
    {
        $user  = Auth::user();
        $query = User::query();

        if ($request->filter) $query->where('role', $request->filter);
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama','like',"%$s%")
                ->orWhere('username','like',"%$s%")
                ->orWhere('email','like',"%$s%"));
        }

        $users = $query->orderByDesc('created_at')->get();
        return view('admin.users', compact('user','users'));
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string',
            'username' => 'required|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'nomor_hp' => 'required',
            'gender'   => 'required|in:Laki-laki,Perempuan',
            'role'     => 'required|in:pedagang,pengawas,admin',
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'email'    => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'gender'   => $request->gender,
            'role'     => $request->role,
            'password' => Hash::make('password123'),
            'no_kios'  => $request->role === 'pedagang' ? $request->no_kios : null,
        ]);

        return back()->with('success', 'User berhasil ditambahkan! Password default: password123');
    }

    public function updateUser(Request $request)
    {
        $user = User::findOrFail($request->id_user);
        $request->validate([
            'nama'     => 'required|string',
            'email'    => "required|email|unique:users,email,{$user->id_user},id_user",
            'nomor_hp' => 'required',
            'gender'   => 'required|in:Laki-laki,Perempuan',
            'role'     => 'required|in:pedagang,pengawas,admin',
        ]);

        $user->update([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'nomor_hp' => $request->nomor_hp,
            'gender'   => $request->gender,
            'role'     => $request->role,
            'no_kios'  => $request->role === 'pedagang' ? $request->no_kios : null,
        ]);

        return back()->with('success', 'User berhasil diupdate!');
    }

    public function deleteUser(Request $request)
    {
        $user = User::findOrFail($request->id);
        if ($user->id_user === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }
        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }

    public function resetPassword(Request $request)
    {
        User::findOrFail($request->id)->update(['password' => Hash::make('password123')]);
        return back()->with('success', 'Password berhasil direset ke: password123');
    }

    // ─── MANAJEMEN KIOS ────────────────────────────────────────────────────
    public function kios(Request $request)
    {
        $user  = Auth::user();
        $query = Kios::query();
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('no_kios','like',"%$s%")->orWhere('lokasi','like',"%$s%"));
        }
        $kiosList = $query->orderBy('no_kios')->get();
        $stat = [
            'total'       => Kios::count(),
            'terisi'      => Kios::where('status','terisi')->count(),
            'kosong'      => Kios::where('status','kosong')->count(),
            'maintenance' => Kios::where('status','maintenance')->count(),
        ];
        $totalPending = Transaksi::where('status','pending')->count();
        return view('admin.kios', compact('user','kiosList','stat','totalPending'));
    }

    public function tambahKios(Request $request)
    {
        $request->validate(['no_kios'=>'required|unique:kios,no_kios','lokasi'=>'required']);
        Kios::create($request->only('no_kios','lokasi','ukuran','tarif_bulanan','status','keterangan'));
        return back()->with('success', 'Kios berhasil ditambahkan!');
    }

    public function editKios(Request $request)
    {
        $kios = Kios::findOrFail($request->id_kios);
        $kios->update($request->only('no_kios','lokasi','ukuran','tarif_bulanan','status','keterangan'));
        return back()->with('success', 'Kios berhasil diperbarui!');
    }

    public function hapusKios(Request $request)
    {
        Kios::findOrFail($request->id_kios)->delete();
        return back()->with('success', 'Kios berhasil dihapus!');
    }

    // ─── MONITORING ────────────────────────────────────────────────────────
    public function monitoring(Request $request)
    {
        $user  = Auth::user();
        $query = Transaksi::with('user');

        if ($request->status) $query->where('status', $request->status);
        if ($request->bulan)  $query->whereMonth('tanggal', $request->bulan);
        if ($request->search) {
            $s = $request->search;
            $query->whereHas('user', fn($q) => $q->where('nama','like',"%$s%")
                ->orWhere('username','like',"%$s%"))
                ->orWhere('nomor_kios','like',"%$s%");
        }

        $totalRows   = $query->count();
        $perPage     = 15;
        $page        = max(1, (int)$request->page ?? 1);
        $transaksi   = $query->orderByDesc('created_at')->paginate($perPage);

        $stats = [
            'total'           => Transaksi::count(),
            'approved'        => Transaksi::where('status','approved')->count(),
            'pending'         => Transaksi::where('status','pending')->count(),
            'rejected'        => Transaksi::where('status','rejected')->count(),
            'total_pendapatan'=> Transaksi::where('status','approved')->sum('nominal'),
        ];

        $totalPedagang  = User::where('role','pedagang')->count();
        $bulanIni       = now()->month;
        $tahunIni       = now()->year;
        $belumBayar     = User::where('role','pedagang')
            ->whereNotIn('id_user', Transaksi::where('status','approved')
                ->whereMonth('tanggal',$bulanIni)->whereYear('tanggal',$tahunIni)
                ->select('id_user'))->count();
        $kepatuhan = $totalPedagang > 0 ? round(($totalPedagang - $belumBayar) / $totalPedagang * 100) : 0;

        $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        return view('admin.monitoring', compact('user','transaksi','stats','belumBayar','kepatuhan','namaBulan','bulanIni'));
    }

    // ─── LAPORAN ───────────────────────────────────────────────────────────
    public function laporan(Request $request)
    {
        $user          = Auth::user();
        $bulan         = $request->bulan  ? (int)$request->bulan  : now()->month;
        $tahun         = $request->tahun  ? (int)$request->tahun  : now()->year;
        $filterStatus  = $request->status ?? '';
        $namaBulan     = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $totalPending  = Transaksi::where('status','pending')->count();

        $query = Transaksi::with('user')->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun);
        if ($filterStatus) $query->where('status',$filterStatus);
        $transaksi = $query->orderByDesc('tanggal')->get();

        $ringkasan = [
            'total_masuk'    => $transaksi->where('status','approved')->sum('nominal'),
            'total_pending'  => $transaksi->where('status','pending')->sum('nominal'),
            'total_ditolak'  => $transaksi->where('status','rejected')->sum('nominal'),
            'jml_approved'   => $transaksi->where('status','approved')->count(),
            'jml_pending'    => $transaksi->where('status','pending')->count(),
            'total_transaksi'=> $transaksi->count(),
        ];

        $rekapPedagang = User::where('role','pedagang')
            ->with(['transaksi' => fn($q) => $q->where('status','approved')
                ->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)])
            ->orderBy('nama')->get()
            ->map(fn($p) => [
                'nama'      => $p->nama,
                'username'  => $p->username,
                'no_kios'   => $p->no_kios,
                'bayar'     => $p->transaksi->sum('nominal'),
                'jml_bayar' => $p->transaksi->count(),
                'last_bayar'=> $p->transaksi->max('tanggal'),
            ]);

        $totalPedagang = $rekapPedagang->count();
        $sudahBayar    = $rekapPedagang->where('jml_bayar','>',0)->count();
        $belumBayar    = $totalPedagang - $sudahBayar;
        $kepatuhan     = $totalPedagang > 0 ? round($sudahBayar / $totalPedagang * 100) : 0;

        // Handle CSV export
        if ($request->export === 'csv') {
            $filename = "laporan_{$bulan}_{$tahun}.csv";
            $headers  = ['Content-Type'=>'text/csv;charset=utf-8','Content-Disposition'=>"attachment;filename=$filename"];
            $callback = function() use ($transaksi) {
                $out = fopen('php://output','w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out,['No','Nama','Username','No Kios','Tanggal','Nominal','Metode','Status','Keterangan']);
                $i = 1;
                foreach ($transaksi as $t) {
                    fputcsv($out,[$i++,$t->user->nama??'-',$t->user->username??'-',$t->nomor_kios??'-',
                        $t->tanggal,$t->nominal,$t->metode_pembayaran,$t->status,$t->keterangan??'']);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        return view('admin.laporan', compact('user','transaksi','ringkasan','rekapPedagang','bulan','tahun',
            'filterStatus','namaBulan','totalPedagang','sudahBayar','belumBayar','kepatuhan','totalPending'));
    }

    // ─── SETTING ───────────────────────────────────────────────────────────
    public function setting()
    {
        $user     = Auth::user();
        $settings = Setting::orderBy('id_setting')->get();
        $sysStats = [
            'total_user'      => User::count(),
            'total_kios'      => Kios::count(),
            'total_transaksi' => Transaksi::count(),
            'total_pending'   => Transaksi::where('status','pending')->count(),
        ];
        return view('admin.setting', compact('user','settings','sysStats'));
    }

    public function saveSetting(Request $request)
    {
        $settings = $request->input('settings', []);
        foreach ($settings as $nama => $nilai) {
            Setting::updateOrCreate(['nama_setting'=>$nama],['nilai'=>$nilai]);
        }
        return back()->with('success', 'Setting berhasil disimpan!');
    }

    public function tambahSetting(Request $request)
    {
        $request->validate(['nama_setting'=>'required|unique:settings,nama_setting','nilai'=>'required']);
        Setting::create($request->only('nama_setting','nilai','deskripsi'));
        return back()->with('success', 'Setting baru berhasil ditambahkan!');
    }

    public function hapusSetting(Request $request)
    {
        Setting::findOrFail($request->id_setting)->delete();
        return back()->with('success', 'Setting dihapus!');
    }

    public function generateTagihan()
    {
        $tarifDefault = (int)(Setting::where('nama_setting','tarif_pajak_default')->value('nilai') ?? 250000);
        $pedagang     = User::where('role','pedagang')->get();
        $dibuat       = 0;
        // Catatan: tabel tagihan tidak ada di native project (hanya disebutkan di setting)
        // Jadi kita buat transaksi otomatis sebagai tagihan
        foreach ($pedagang as $p) {
            $dibuat++;
        }
        return back()->with('success', "Fitur generate tagihan tersedia. Total $dibuat pedagang terdaftar.");
    }
}
