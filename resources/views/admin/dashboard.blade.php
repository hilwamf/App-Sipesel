<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;}.stat-card{transition:transform .3s,box-shadow .3s;}.stat-card:hover{transform:translateY(-5px);box-shadow:0 10px 25px rgba(0,0,0,.3);}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>
@php $totalPending = \App\Models\Transaksi::where('status','pending')->count(); @endphp
@include('layouts.admin-header')

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">Dashboard Administrator</h1>
            <p class="text-sm md:text-base opacity-90">Halo, <span class="text-yellow-400 font-semibold">{{ $user->username }}</span> 👋<br>Kelola seluruh sistem pembayaran pajak kios dengan mudah</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-gradient-to-br from-green-600/40 to-green-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-500/30 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <span class="text-xs bg-green-500/30 px-2 py-1 rounded-full">Total</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-1">Rp {{ number_format($totalPendapatan,0,',','.') }}</h3>
                <p class="text-sm opacity-80">Total Pendapatan</p>
            </div>
            <div class="stat-card bg-gradient-to-br from-red-600/40 to-red-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-red-500/30 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                    <span class="text-xs bg-red-500/30 px-2 py-1 rounded-full">Alert</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-1">{{ $totalPending }}</h3>
                <p class="text-sm opacity-80">Pembayaran Pending</p>
            </div>
            <div class="stat-card bg-gradient-to-br from-blue-600/40 to-blue-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-500/30 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                    <span class="text-xs bg-blue-500/30 px-2 py-1 rounded-full">Aktif</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-1">{{ $totalPedagang }}</h3>
                <p class="text-sm opacity-80">Total Pedagang</p>
            </div>
            <div class="stat-card bg-gradient-to-br from-purple-600/40 to-purple-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-500/30 p-3 rounded-lg"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                    <span class="text-xs bg-purple-500/30 px-2 py-1 rounded-full">Status</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-1">{{ $totalKiosTerisi }} / {{ $totalKios }}</h3>
                <p class="text-sm opacity-80">Kios Terisi / Total</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-1 bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Quick Actions
                </h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.verifikasi') }}" class="block bg-yellow-500/20 hover:bg-yellow-500/30 border border-yellow-500/50 rounded-lg p-4 transition-all">
                        <div class="flex items-center justify-between">
                            <div><h3 class="font-semibold">Verifikasi Pembayaran</h3><p class="text-xs opacity-80">{{ $totalPending }} pending</p></div>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    <a href="{{ route('admin.users') }}" class="block bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/50 rounded-lg p-4 transition-all">
                        <div class="flex items-center justify-between">
                            <div><h3 class="font-semibold">Kelola Pengguna</h3><p class="text-xs opacity-80">{{ $totalPedagang + $totalPengawas }} users</p></div>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    <a href="{{ route('admin.laporan') }}" class="block bg-green-500/20 hover:bg-green-500/30 border border-green-500/50 rounded-lg p-4 transition-all">
                        <div class="flex items-center justify-between">
                            <div><h3 class="font-semibold">Export Laporan</h3><p class="text-xs opacity-80">Download reports</p></div>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                    <a href="{{ route('admin.kios') }}" class="block bg-purple-500/20 hover:bg-purple-500/30 border border-purple-500/50 rounded-lg p-4 transition-all">
                        <div class="flex items-center justify-between">
                            <div><h3 class="font-semibold">Kelola Kios</h3><p class="text-xs opacity-80">{{ $totalKios }} kios</p></div>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <h2 class="text-xl font-bold mb-4">Transaksi Terbaru</h2>
                @if($transaksiTerbaru->count() > 0)
                <div class="space-y-3">
                    @foreach($transaksiTerbaru as $trx)
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-semibold">{{ $trx->user->nama ?? '-' }}</h3>
                                <p class="text-xs opacity-80">Kios: {{ $trx->nomor_kios }} | {{ $trx->metode_pembayaran }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $trx->status==='approved'?'bg-green-500/30 text-green-300':($trx->status==='pending'?'bg-yellow-500/30 text-yellow-300':'bg-red-500/30 text-red-300') }}">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span>Rp {{ number_format($trx->nominal,0,',','.') }}</span>
                            <span class="text-xs opacity-70">{{ $trx->tanggal->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 opacity-60"><p>Belum ada transaksi</p></div>
                @endif
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.monitoring') }}" class="text-yellow-400 hover:text-yellow-300 text-sm font-medium">Lihat Semua Transaksi →</a>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
            <h2 class="text-xl font-bold mb-4">Informasi Sistem</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-white/5 rounded-lg p-4"><p class="opacity-70 mb-1">Total Pengguna</p><p class="text-lg font-semibold">{{ $totalPedagang + $totalPengawas + 1 }} users</p></div>
                <div class="bg-white/5 rounded-lg p-4"><p class="opacity-70 mb-1">Transaksi Terbaru</p><p class="text-lg font-semibold">{{ $transaksiTerbaru->count() }} transaksi</p></div>
                <div class="bg-white/5 rounded-lg p-4"><p class="opacity-70 mb-1">Kios Terisi</p><p class="text-lg font-semibold">{{ $totalKios > 0 ? round($totalKiosTerisi/$totalKios*100) : 0 }}%</p></div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.
</footer>
</body>
</html>
