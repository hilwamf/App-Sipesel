<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Monitoring - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
<div class="max-w-7xl mx-auto">
    <div class="mb-6"><h1 class="text-3xl font-bold">Monitoring Transaksi</h1><p class="text-sm opacity-80">Pantau seluruh aktivitas pembayaran sistem</p></div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="col-span-2 bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30">
            <p class="text-xs opacity-70">Total Pendapatan</p>
            <p class="text-xl font-bold text-green-300 mt-1">Rp {{ number_format($stats['total_pendapatan'],0,',','.') }}</p>
            <p class="text-xs opacity-50 mt-1">All time approved</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
            <p class="text-2xl font-bold">{{ $stats['total'] }}</p><p class="text-xs opacity-60 mt-1">Total Transaksi</p>
        </div>
        <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center">
            <p class="text-2xl font-bold text-yellow-300">{{ $stats['pending'] }}</p><p class="text-xs opacity-60 mt-1">Pending</p>
        </div>
        <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30 text-center">
            <p class="text-2xl font-bold text-red-300">{{ $belumBayar }}</p><p class="text-xs opacity-60 mt-1">Kios Blm Bayar</p>
        </div>
        <div class="bg-blue-500/20 backdrop-blur-md rounded-xl p-4 border border-blue-400/30 text-center">
            <p class="text-2xl font-bold text-blue-300">{{ $kepatuhan }}%</p><p class="text-xs opacity-60 mt-1">Kepatuhan</p>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold">Tingkat Kepatuhan Bulan {{ $namaBulan[$bulanIni] }}</span>
            <span class="text-sm font-bold {{ $kepatuhan>=80?'text-green-300':($kepatuhan>=50?'text-yellow-300':'text-red-300') }}">{{ $kepatuhan }}%</span>
        </div>
        <div class="w-full bg-white/10 rounded-full h-3">
            <div class="h-3 rounded-full {{ $kepatuhan>=80?'bg-green-400':($kepatuhan>=50?'bg-yellow-400':'bg-red-400') }}" style="width:{{ $kepatuhan }}%"></div>
        </div>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / username / kios..."
                class="bg-white/10 border border-white/30 rounded-lg px-4 py-2 text-sm text-white placeholder-white/50 focus:outline-none focus:border-yellow-400 flex-1 min-w-48">
            <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
            </select>
            <select name="bulan" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                <option value="">Semua Bulan</option>
                @foreach($namaBulan as $i => $nb)@if($i>0)<option value="{{ $i }}" {{ request('bulan')==$i?'selected':'' }}>{{ $nb }}</option>@endif@endforeach
            </select>
            <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Filter</button>
            <a href="{{ route('admin.monitoring') }}" class="text-green-300 hover:text-white text-sm">Reset</a>
            <span class="text-xs opacity-50 ml-auto">{{ $transaksi->total() }} data</span>
        </form>
    </div>

    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-green-800/60 text-left">
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Pedagang</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No Kios</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-right">Nominal</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Periode</th>
                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-center">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($transaksi as $t)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-4 py-3">
                            <div class="text-xs">{{ $t->tanggal->format('d M Y') }}</div>
                            <div class="text-xs opacity-50">{{ $t->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $t->user->nama ?? '-' }}</div>
                            <div class="text-xs opacity-60">{{ $t->user->username ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-yellow-300 text-sm">{{ $t->nomor_kios ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="px-4 py-3 text-xs opacity-80">{{ $t->metode_pembayaran }}</td>
                        <td class="px-4 py-3 text-xs">{{ $namaBulan[$t->tanggal->month] }} {{ $t->tanggal->year }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $t->status==='approved'?'bg-green-500/30 text-green-300':($t->status==='pending'?'bg-yellow-500/30 text-yellow-300':'bg-red-500/30 text-red-300') }}">{{ ucfirst($t->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center opacity-50">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($transaksi->hasPages())
    <div class="flex items-center justify-center gap-2">
        @if($transaksi->onFirstPage())
        <span class="px-3 py-2 bg-white/5 rounded-lg text-sm opacity-40">← Prev</span>
        @else
        <a href="{{ $transaksi->previousPageUrl() }}" class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-all">← Prev</a>
        @endif
        @foreach($transaksi->getUrlRange(max(1,$transaksi->currentPage()-2), min($transaksi->lastPage(),$transaksi->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}" class="px-3 py-2 rounded-lg text-sm transition-all {{ $page==$transaksi->currentPage()?'bg-yellow-400 text-green-900 font-bold':'bg-white/10 hover:bg-white/20' }}">{{ $page }}</a>
        @endforeach
        @if($transaksi->hasMorePages())
        <a href="{{ $transaksi->nextPageUrl() }}" class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-all">Next →</a>
        @else
        <span class="px-3 py-2 bg-white/5 rounded-lg text-sm opacity-40">Next →</span>
        @endif
    </div>
    @endif

</div>
</section>
<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">&copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.</footer>
</body>
</html>
