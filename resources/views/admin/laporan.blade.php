<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Laporan - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;}
@media print{.no-print{display:none!important;}body{background:white!important;color:black!important;}.print-section{background:white!important;color:black!important;}}
</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4 no-print">
        <div><h1 class="text-3xl font-bold">Laporan Pembayaran</h1><p class="text-sm opacity-80">{{ $namaBulan[$bulan] }} {{ $tahun }}</p></div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.laporan', array_merge(request()->all(), ['export'=>'csv'])) }}" class="bg-green-500/80 hover:bg-green-500 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Export CSV
            </a>
            <button onclick="window.print()" class="bg-blue-500/80 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div><label class="text-xs opacity-70 block mb-1">Bulan</label>
                <select name="bulan" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    @foreach($namaBulan as $i=>$nb)@if($i>0)<option value="{{ $i }}" {{ $i==$bulan?'selected':'' }}>{{ $nb }}</option>@endif@endforeach
                </select>
            </div>
            <div><label class="text-xs opacity-70 block mb-1">Tahun</label>
                <select name="tahun" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    @for($y=2024;$y<=2027;$y++)<option value="{{ $y }}" {{ $y==$tahun?'selected':'' }}>{{ $y }}</option>@endfor
                </select>
            </div>
            <div><label class="text-xs opacity-70 block mb-1">Status</label>
                <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    <option value="">Semua</option>
                    <option value="approved" {{ $filterStatus=='approved'?'selected':'' }}>Approved</option>
                    <option value="pending" {{ $filterStatus=='pending'?'selected':'' }}>Pending</option>
                    <option value="rejected" {{ $filterStatus=='rejected'?'selected':'' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Tampilkan</button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30">
            <p class="text-xs opacity-70 mb-1">Total Masuk</p>
            <p class="text-xl font-bold text-green-300">Rp {{ number_format($ringkasan['total_masuk'],0,',','.') }}</p>
            <p class="text-xs opacity-60 mt-1">{{ $ringkasan['jml_approved'] }} transaksi</p>
        </div>
        <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30">
            <p class="text-xs opacity-70 mb-1">Pending</p>
            <p class="text-xl font-bold text-yellow-300">Rp {{ number_format($ringkasan['total_pending'],0,',','.') }}</p>
            <p class="text-xs opacity-60 mt-1">{{ $ringkasan['jml_pending'] }} transaksi</p>
        </div>
        <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30">
            <p class="text-xs opacity-70 mb-1">Kios Belum Bayar</p>
            <p class="text-xl font-bold text-red-300">{{ $belumBayar }} kios</p>
            <p class="text-xs opacity-60 mt-1">dari {{ $totalPedagang }} pedagang</p>
        </div>
        <div class="bg-blue-500/20 backdrop-blur-md rounded-xl p-4 border border-blue-400/30">
            <p class="text-xs opacity-70 mb-1">Tingkat Kepatuhan</p>
            <p class="text-xl font-bold text-blue-300">{{ $kepatuhan }}%</p>
            <p class="text-xs opacity-60 mt-1">{{ $sudahBayar }}/{{ $totalPedagang }} pedagang</p>
        </div>
    </div>

    <div class="mb-4 no-print flex gap-2">
        <button onclick="showTab('transaksi')" id="tab-transaksi" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm">Detail Transaksi</button>
        <button onclick="showTab('rekap')" id="tab-rekap" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all">Rekap Per Pedagang</button>
    </div>

    <div id="panel-transaksi" class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-6">
        <div class="px-4 py-3 bg-green-800/60 font-semibold text-sm">
            Detail Transaksi — {{ $namaBulan[$bulan] }} {{ $tahun }}
            <span class="ml-2 text-xs opacity-70">({{ $transaksi->count() }} data)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-xs opacity-70">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">No Kios</th>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-right">Nominal</th>
                    <th class="px-4 py-2 text-left">Metode</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($transaksi as $i=>$t)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-2 opacity-60">{{ $i+1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $t->user->nama ?? '-' }}</td>
                        <td class="px-4 py-2 font-mono text-yellow-300">{{ $t->nomor_kios ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $t->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-right font-mono">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="px-4 py-2 text-xs opacity-80">{{ $t->metode_pembayaran }}</td>
                        <td class="px-4 py-2 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $t->status==='approved'?'bg-green-500/30 text-green-300':($t->status==='pending'?'bg-yellow-500/30 text-yellow-300':'bg-red-500/30 text-red-300') }}">{{ ucfirst($t->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center opacity-50">Tidak ada transaksi pada periode ini</td></tr>
                    @endforelse
                </tbody>
                @if($transaksi->count()>0)
                <tfoot><tr class="border-t border-white/20 bg-green-800/30 font-semibold">
                    <td colspan="4" class="px-4 py-3 text-right text-xs uppercase tracking-wider opacity-70">Total Approved:</td>
                    <td class="px-4 py-3 text-right text-green-300">Rp {{ number_format($ringkasan['total_masuk'],0,',','.') }}</td>
                    <td colspan="2"></td>
                </tr></tfoot>
                @endif
            </table>
        </div>
    </div>

    <div id="panel-rekap" class="hidden bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-6">
        <div class="px-4 py-3 bg-green-800/60 font-semibold text-sm">Rekap Per Pedagang — {{ $namaBulan[$bulan] }} {{ $tahun }}</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-white/10 text-xs opacity-70">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama Pedagang</th>
                    <th class="px-4 py-2 text-left">No Kios</th>
                    <th class="px-4 py-2 text-right">Total Bayar</th>
                    <th class="px-4 py-2 text-center">Tanggal Bayar</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($rekapPedagang as $i=>$p)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-2 opacity-60">{{ $i+1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $p['nama'] }}</td>
                        <td class="px-4 py-2 font-mono text-yellow-300">{{ $p['no_kios'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-right font-mono">{{ $p['bayar']>0 ? 'Rp '.number_format($p['bayar'],0,',','.') : '-' }}</td>
                        <td class="px-4 py-2 text-center text-xs">{{ $p['last_bayar'] ? \Carbon\Carbon::parse($p['last_bayar'])->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            @if($p['jml_bayar']>0)<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500/30 text-green-300">Sudah Bayar</span>
                            @else<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-500/30 text-red-300">Belum Bayar</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10 no-print">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>
<script>
function showTab(tab){
    document.getElementById('panel-transaksi').classList.add('hidden');
    document.getElementById('panel-rekap').classList.add('hidden');
    document.getElementById('tab-transaksi').className='px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all';
    document.getElementById('tab-rekap').className='px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all';
    document.getElementById('panel-'+tab).classList.remove('hidden');
    document.getElementById('tab-'+tab).className='px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm';
}
</script>
</body>
</html>
