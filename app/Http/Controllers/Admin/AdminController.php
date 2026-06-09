<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Kios;
use App\Models\Notifikasi;
use App\Models\Setting;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user             = Auth::user();
        $totalPendapatan  = Transaksi::where('status','approved')->sum('nominal');
        $totalPedagang    = User::where('role','pedagang')->count();
        $totalPengawas    = User::where('role','pengawas')->count();
        $totalPending     = Transaksi::where('status','pending')->count();
        $totalKios        = Kios::count();
        $totalKiosTerisi  = Kios::where('status','terisi')->count();
        $transaksiTerbaru = Transaksi::with('user')->orderByDesc('created_at')->limit(5)->get();

        // Data chart - pendapatan 6 bulan terakhir
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartData[] = [
                'bulan'      => $date->locale('id')->isoFormat('MMM YY'),
                'pendapatan' => (int) Transaksi::where('status','approved')
                    ->whereMonth('tanggal', $date->month)
                    ->whereYear('tanggal', $date->year)
                    ->sum('nominal'),
            ];
        }

        // Data chart - status transaksi
        $statusChart = [
            'approved' => Transaksi::where('status','approved')->count(),
            'pending'  => Transaksi::where('status','pending')->count(),
            'rejected' => Transaksi::where('status','rejected')->count(),
        ];

        return view('admin.dashboard', compact(
            'user','totalPendapatan','totalPedagang','totalPengawas',
            'totalPending','totalKios','totalKiosTerisi','transaksiTerbaru',
            'chartData','statusChart'
        ));
    }

    public function verifikasi()
    {
        $user     = Auth::user();
        $pending  = Transaksi::with('user')->where('status','pending')->orderByDesc('created_at')->get();
        $verified = Transaksi::with(['user','verifier'])->whereIn('status',['approved','rejected'])->orderByDesc('verified_at')->limit(20)->get();
        return view('admin.verifikasi', compact('user','pending','verified'));
    }

    public function prosesVerifikasi(Request $request)
    {
        $request->validate(['id_transaksi'=>'required','action'=>'required|in:approve,reject']);
        $trx = Transaksi::with('user')->findOrFail($request->id_transaksi);

        if ($request->action === 'approve') {
            // ACID - Atomic: update transaksi + kirim notifikasi ke pedagang
            DB::transaction(function () use ($trx) {
                $trx->update([
                    'status'             => 'approved',
                    'verified_by'        => Auth::id(),
                    'verified_at'        => now(),
                    'catatan_verifikasi' => 'Pembayaran disetujui oleh admin',
                ]);
                Notifikasi::create([
                    'id_user'     => $trx->id_user,
                    'id_pengirim' => Auth::id(),
                    'pesan'       => "Pembayaran pajak kios {$trx->nomor_kios} ({$trx->jenis_pajak}) sebesar Rp " . number_format($trx->nominal,0,',','.') . " telah DISETUJUI. Terima kasih!",
                    'dibaca'      => false,
                ]);
            });
            return back()->with('success','Pembayaran berhasil disetujui!');
        }

        $request->validate(['catatan_penolakan'=>'required|string']);
        // ACID - Atomic: update transaksi + kirim notifikasi penolakan
        DB::transaction(function () use ($trx, $request) {
            $trx->update([
                'status'             => 'rejected',
                'verified_by'        => Auth::id(),
                'verified_at'        => now(),
                'catatan_verifikasi' => $request->catatan_penolakan,
            ]);
            Notifikasi::create([
                'id_user'     => $trx->id_user,
                'id_pengirim' => Auth::id(),
                'pesan'       => "Pembayaran pajak kios {$trx->nomor_kios} DITOLAK. Alasan: {$request->catatan_penolakan}",
                'dibaca'      => false,
            ]);
        });
        return back()->with('success','Pembayaran berhasil ditolak!');
    }

    public function users(Request $request)
    {
        $user  = Auth::user();
        $query = User::query();
        if ($request->filter) $query->where('role',$request->filter);
        if ($request->search) { $s=$request->search; $query->where(fn($q)=>$q->where('nama','like',"%$s%")->orWhere('username','like',"%$s%")->orWhere('email','like',"%$s%")); }
        $users = $query->orderByDesc('created_at')->get();
        return view('admin.users', compact('user','users'));
    }

    public function addUser(Request $request)
    {
        $request->validate(['nama'=>'required','username'=>'required|unique:users,username','email'=>'required|email|unique:users,email','nomor_hp'=>'required','gender'=>'required|in:Laki-laki,Perempuan','role'=>'required|in:pedagang,pengawas,admin']);
        DB::transaction(function () use ($request) {
            User::create(['nama'=>$request->nama,'username'=>$request->username,'email'=>$request->email,'nomor_hp'=>$request->nomor_hp,'gender'=>$request->gender,'role'=>$request->role,'password'=>Hash::make('password123'),'no_kios'=>$request->role==='pedagang'?$request->no_kios:null]);
        });
        return back()->with('success','User berhasil ditambahkan! Password default: password123');
    }

    public function updateUser(Request $request)
    {
        $user = User::findOrFail($request->id_user);
        $request->validate(['nama'=>'required','email'=>"required|email|unique:users,email,{$user->id_user},id_user",'nomor_hp'=>'required','gender'=>'required|in:Laki-laki,Perempuan','role'=>'required|in:pedagang,pengawas,admin']);
        DB::transaction(function () use ($user, $request) {
            $user->update(['nama'=>$request->nama,'email'=>$request->email,'nomor_hp'=>$request->nomor_hp,'gender'=>$request->gender,'role'=>$request->role,'no_kios'=>$request->role==='pedagang'?$request->no_kios:null]);
        });
        return back()->with('success','User berhasil diupdate!');
    }

    public function deleteUser(Request $request)
    {
        $user = User::findOrFail($request->id);
        if ($user->id_user === Auth::id()) return back()->with('error','Tidak dapat menghapus akun sendiri!');
        DB::transaction(fn() => $user->delete());
        return back()->with('success','User berhasil dihapus!');
    }

    public function resetPassword(Request $request)
    {
        DB::transaction(fn() => User::findOrFail($request->id)->update(['password'=>Hash::make('password123')]));
        return back()->with('success','Password direset ke: password123');
    }

    public function kios(Request $request)
    {
        $user  = Auth::user();
        $query = Kios::query();
        if ($request->status) $query->where('status',$request->status);
        if ($request->search) { $s=$request->search; $query->where(fn($q)=>$q->where('no_kios','like',"%$s%")->orWhere('lokasi','like',"%$s%")); }
        $kiosList = $query->orderBy('no_kios')->get();
        $stat = ['total'=>Kios::count(),'terisi'=>Kios::where('status','terisi')->count(),'kosong'=>Kios::where('status','kosong')->count(),'maintenance'=>Kios::where('status','maintenance')->count()];
        $totalPending = Transaksi::where('status','pending')->count();
        return view('admin.kios', compact('user','kiosList','stat','totalPending'));
    }

    public function tambahKios(Request $request)
    {
        $request->validate(['no_kios'=>'required|unique:kios,no_kios','lokasi'=>'required']);
        DB::transaction(fn() => Kios::create($request->only('no_kios','lokasi','ukuran','tarif_bulanan','status','keterangan')));
        return back()->with('success','Kios berhasil ditambahkan!');
    }

    public function editKios(Request $request)
    {
        DB::transaction(fn() => Kios::findOrFail($request->id_kios)->update($request->only('no_kios','lokasi','ukuran','tarif_bulanan','status','keterangan')));
        return back()->with('success','Kios berhasil diperbarui!');
    }

    public function hapusKios(Request $request)
    {
        DB::transaction(fn() => Kios::findOrFail($request->id_kios)->delete());
        return back()->with('success','Kios berhasil dihapus!');
    }

    public function monitoring(Request $request)
    {
        $user  = Auth::user();
        $query = Transaksi::with('user');
        if ($request->status) $query->where('status',$request->status);
        if ($request->bulan)  $query->whereMonth('tanggal',$request->bulan);
        if ($request->search) { $s=$request->search; $query->whereHas('user',fn($q)=>$q->where('nama','like',"%$s%")->orWhere('username','like',"%$s%"))->orWhere('nomor_kios','like',"%$s%"); }
        $transaksi     = $query->orderByDesc('created_at')->paginate(15);
        $stats         = ['total'=>Transaksi::count(),'approved'=>Transaksi::where('status','approved')->count(),'pending'=>Transaksi::where('status','pending')->count(),'rejected'=>Transaksi::where('status','rejected')->count(),'total_pendapatan'=>Transaksi::where('status','approved')->sum('nominal')];
        $totalPedagang = User::where('role','pedagang')->count();
        $bulanIni      = now()->month;
        $belumBayar    = User::where('role','pedagang')->whereNotIn('id_user',Transaksi::where('status','approved')->whereMonth('tanggal',$bulanIni)->whereYear('tanggal',now()->year)->select('id_user'))->count();
        $kepatuhan     = $totalPedagang > 0 ? round(($totalPedagang-$belumBayar)/$totalPedagang*100) : 0;
        $namaBulan     = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return view('admin.monitoring', compact('user','transaksi','stats','belumBayar','kepatuhan','namaBulan','bulanIni'));
    }

    public function laporan(Request $request)
    {
        $user         = Auth::user();
        $bulan        = (int)($request->bulan ?? now()->month);
        $tahun        = (int)($request->tahun ?? now()->year);
        $filterStatus = $request->status ?? '';
        $namaBulan    = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $totalPending = Transaksi::where('status','pending')->count();

        $query = Transaksi::with('user')->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun);
        if ($filterStatus) $query->where('status',$filterStatus);
        $transaksi = $query->orderByDesc('tanggal')->get();

        $ringkasan     = ['total_masuk'=>$transaksi->where('status','approved')->sum('nominal'),'total_pending'=>$transaksi->where('status','pending')->sum('nominal'),'total_ditolak'=>$transaksi->where('status','rejected')->sum('nominal'),'jml_approved'=>$transaksi->where('status','approved')->count(),'jml_pending'=>$transaksi->where('status','pending')->count(),'total_transaksi'=>$transaksi->count()];
        $rekapPedagang = User::where('role','pedagang')->with(['transaksi'=>fn($q)=>$q->where('status','approved')->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)])->orderBy('nama')->get()->map(fn($p)=>['id_user'   => $p->id_user,'nama'=>$p->nama,'username'=>$p->username,'no_kios'=>$p->no_kios,'bayar'=>$p->transaksi->sum('nominal'),'jml_bayar'=>$p->transaksi->count(),'last_bayar'=>$p->transaksi->max('tanggal')]);
        $totalPedagang = $rekapPedagang->count();
        $sudahBayar    = $rekapPedagang->where('jml_bayar','>',0)->count();
        $belumBayar    = $totalPedagang - $sudahBayar;
        $kepatuhan     = $totalPedagang > 0 ? round($sudahBayar/$totalPedagang*100) : 0;

        if ($request->export === 'csv') {
            $headers = ['Content-Type'=>'text/csv;charset=utf-8','Content-Disposition'=>"attachment;filename=laporan_{$bulan}_{$tahun}.csv"];
            return response()->stream(function() use ($transaksi) {
                $out = fopen('php://output','w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out,['No','Nama','Username','No Kios','Tanggal','Nominal','Metode','Status']);
                foreach ($transaksi as $i => $t) fputcsv($out,[$i+1,$t->user->nama??'-',$t->user->username??'-',$t->nomor_kios??'-',$t->tanggal,$t->nominal,$t->metode_pembayaran,$t->status]);
                fclose($out);
            }, 200, $headers);
        }

        if ($request->export === 'pdf') {
            return view('admin.laporan-pdf', compact('transaksi','ringkasan','rekapPedagang','bulan','tahun','namaBulan','totalPedagang','sudahBayar','belumBayar','kepatuhan'));
        }

        return view('admin.laporan', compact('user','transaksi','ringkasan','rekapPedagang','bulan','tahun','filterStatus','namaBulan','totalPedagang','sudahBayar','belumBayar','kepatuhan','totalPending'));
    }

    public function setting()
    {
        $user     = Auth::user();
        $settings = Setting::orderBy('id_setting')->get();
        $sysStats = ['total_user'=>User::count(),'total_kios'=>Kios::count(),'total_transaksi'=>Transaksi::count(),'total_pending'=>Transaksi::where('status','pending')->count()];
        return view('admin.setting', compact('user','settings','sysStats'));
    }

    public function saveSetting(Request $request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request->input('settings',[]) as $nama => $nilai) {
                Setting::updateOrCreate(['nama_setting'=>$nama],['nilai'=>$nilai]);
            }
        });
        return back()->with('success','Setting berhasil disimpan!');
    }

    public function tambahSetting(Request $request)
    {
        $request->validate(['nama_setting'=>'required|unique:settings,nama_setting','nilai'=>'required']);
        DB::transaction(fn() => Setting::create($request->only('nama_setting','nilai','deskripsi')));
        return back()->with('success','Setting baru ditambahkan!');
    }

    public function hapusSetting(Request $request)
    {
        DB::transaction(fn() => Setting::findOrFail($request->id_setting)->delete());
        return back()->with('success','Setting dihapus!');
    }

    public function generateTagihan(Request $request)
    {
        // Simpan semua setting dari form jika ada
        if ($request->has('settings')) {
            DB::transaction(function () use ($request) {
                foreach ($request->input('settings', []) as $nama => $nilai) {
                    Setting::updateOrCreate(
                        ['nama_setting' => $nama],
                        ['nilai' => $nilai]
                    );
                }
            });
        }

        // Ambil tarif terbaru
        $settings   = Setting::whereIn('nama_setting', ['harian_rate','mingguan_rate','bulanan_rate'])->pluck('nilai','nama_setting');
        $harian     = number_format((int)($settings['harian_rate']   ?? 5000), 0, ',', '.');
        $mingguan   = number_format((int)($settings['mingguan_rate'] ?? 35000), 0, ',', '.');
        $bulanan    = number_format((int)($settings['bulanan_rate']  ?? 150000), 0, ',', '.');

        // Kirim notifikasi ke semua pedagang
        $pedagangList = User::where('role', 'pedagang')->get();
        $bulan        = now()->locale('id')->isoFormat('MMMM YYYY');

        DB::transaction(function () use ($pedagangList, $harian, $mingguan, $bulanan, $bulan) {
            foreach ($pedagangList as $p) {
                Notifikasi::create([
                    'id_user'     => $p->id_user,
                    'id_pengirim' => Auth::id(),
                    'pesan'       => "Info Tarif Pajak {$bulan}: Harian Rp {$harian} | Mingguan Rp {$mingguan} | Bulanan Rp {$bulanan}. Segera lakukan pembayaran sebelum jatuh tempo.",
                    'dibaca'      => false,
                ]);
            }
        });

        return back()->with('success', "Tarif berhasil disimpan & notifikasi dikirim ke {$pedagangList->count()} pedagang!");
    }

    public function berita()
    {
        $user   = Auth::user();
        $berita = Berita::orderByDesc('created_at')->get();
        return view('admin.berita', compact('user','berita'));
    }

    public function tambahBerita(Request $request)
    {
        $request->validate(['judul'=>'required|string|max:255','isi'=>'required|string','thumbnail'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048']);
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file     = $request->file('thumbnail');
            $filename = 'berita_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/berita'), $filename);
            $thumbnailPath = 'uploads/berita/'.$filename;
        }
        DB::transaction(fn() => Berita::create(['judul'=>$request->judul,'isi'=>$request->isi,'thumbnail'=>$thumbnailPath,'status'=>$request->status??'aktif']));
        return back()->with('success','Berita berhasil ditambahkan!');
    }

    public function editBerita(Request $request)
    {
        $request->validate(['judul'=>'required|string|max:255','isi'=>'required|string','thumbnail'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048']);
        $berita = Berita::findOrFail($request->id);
        $thumbnailPath = $berita->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($berita->thumbnail && file_exists(public_path($berita->thumbnail))) unlink(public_path($berita->thumbnail));
            $file = $request->file('thumbnail');
            $filename = 'berita_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/berita'), $filename);
            $thumbnailPath = 'uploads/berita/'.$filename;
        }
        DB::transaction(fn() => $berita->update(['judul'=>$request->judul,'isi'=>$request->isi,'thumbnail'=>$thumbnailPath,'status'=>$request->status]));
        return back()->with('success','Berita berhasil diperbarui!');
    }

    public function hapusBerita(Request $request)
    {
        $berita = Berita::findOrFail($request->id);
        if ($berita->thumbnail && file_exists(public_path($berita->thumbnail))) unlink(public_path($berita->thumbnail));
        DB::transaction(fn() => $berita->delete());
        return back()->with('success','Berita dihapus!');
    }

    public function toggleBerita(Request $request)
    {
        $berita = Berita::findOrFail($request->id);
        DB::transaction(fn() => $berita->update(['status'=>$berita->status==='aktif'?'nonaktif':'aktif']));
        return back()->with('success','Status berita diperbarui!');
    }

    public function kirimNotifLaporan(Request $request)
    {
        $idUser = (int)$request->id_user;
        $nama   = $request->nama;
        $kios   = $request->kios;

        if (!$idUser) return response()->json(['success'=>false,'message'=>'ID tidak valid']);

        $pesan = $request->filled('pesan_custom')
            ? $request->pesan_custom
            : "Pengingat dari Admin: Tagihan pajak kios {$kios} Anda belum dibayar bulan ini. Segera lakukan pembayaran melalui menu Pembayaran.";

        DB::transaction(function () use ($idUser, $pesan) {
            Notifikasi::create([
                'id_user'     => $idUser,
                'id_pengirim' => Auth::id(),
                'pesan'       => $pesan,
                'dibaca'      => false,
            ]);
        });

        return response()->json(['success' => true]);
    }

}