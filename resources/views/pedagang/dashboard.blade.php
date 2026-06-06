<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;} #notifPanel.show{display:block!important;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>

@php $showNotif = true; @endphp
@include('layouts.pedagang-header')

<section class="relative z-10 px-6 md:px-20 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto">

        <div class="mb-10">
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-2">Selamat Datang,</h1>
            <h2 class="text-2xl text-yellow-400 font-semibold">{{ $user->nama }} 👋</h2>
            <p class="text-sm opacity-70 mt-2">Kios <span class="font-semibold text-white">{{ $user->no_kios }}</span></p>
        </div>

        {{-- Alert jatuh tempo --}}
        @if($sudahLewat)
        <div class="bg-red-500/20 border border-red-400/40 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-semibold text-red-300 text-sm">Tagihan Anda Sudah Jatuh Tempo!</p>
                <p class="text-xs text-red-200/80 mt-0.5">Jatuh tempo: {{ $jatuhTempoStr }} (terlambat {{ $sisaHari }} hari). Segera lakukan pembayaran.</p>
                <a href="{{ route('pedagang.pembayaran') }}" class="inline-block mt-2 px-4 py-1.5 bg-red-500/40 hover:bg-red-500/60 border border-red-400/50 rounded-lg text-xs font-semibold transition-all">Bayar Sekarang</a>
            </div>
        </div>
        @elseif($jatuhTempoStr && $sisaHari <= 3)
        <div class="bg-yellow-500/20 border border-yellow-400/40 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-semibold text-yellow-300 text-sm">Tagihan Anda Segera Jatuh Tempo</p>
                <p class="text-xs text-yellow-200/80 mt-0.5">Sisa {{ $sisaHari }} hari lagi ({{ $jatuhTempoStr }}). Jangan sampai terlambat!</p>
            </div>
        </div>
        @endif

        {{-- Info Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Tagihan Berikutnya</p>
                @if($jatuhTempoStr)
                    <p class="text-lg font-bold">{{ $jatuhTempoStr }}</p>
                    <p class="text-xs mt-1 {{ $sudahLewat ? 'text-red-300' : 'text-green-300' }}">
                        {{ $sudahLewat ? 'Terlambat '.$sisaHari.' hari' : 'Sisa '.$sisaHari.' hari' }}
                    </p>
                @else
                    <p class="text-sm text-white/50">Belum ada transaksi</p>
                @endif
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Jenis Pajak Terakhir</p>
                @if($lastTrx)
                    <p class="text-lg font-bold">{{ $lastTrx->jenis_pajak }}</p>
                    <p class="text-xs mt-1 text-white/50">Rp {{ number_format($lastTrx->nominal, 0, ',', '.') }}</p>
                @else
                    <p class="text-sm text-white/50">-</p>
                @endif
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Nomor Kios</p>
                <p class="text-2xl font-bold tracking-widest" style="font-family:'Montserrat',sans-serif;">{{ $user->no_kios ?? '-' }}</p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('pedagang.pembayaran') }}" class="bg-green-700/60 hover:bg-green-700/80 border border-green-500/40 rounded-xl p-5 flex items-center gap-4 transition-all hover:-translate-y-0.5">
                <div class="w-12 h-12 bg-green-500/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <p class="font-semibold">Bayar Pajak</p>
                    <p class="text-xs text-white/60">Lakukan pembayaran sekarang</p>
                </div>
            </a>
            <a href="{{ route('pedagang.riwayat') }}" class="bg-white/10 hover:bg-white/15 border border-white/20 rounded-xl p-5 flex items-center gap-4 transition-all hover:-translate-y-0.5">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="font-semibold">Riwayat Bayar</p>
                    <p class="text-xs text-white/60">Lihat semua transaksi Anda</p>
                </div>
            </a>
        </div>
    </div>
</section>

<script>
function toggleNotif() {
    const panel = document.getElementById('notifPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const panel = document.getElementById('notifPanel');
    if (panel && !panel.contains(e.target) && !e.target.closest('[onclick="toggleNotif()"]')) {
        panel.style.display = 'none';
    }
});
</script>
</body>
</html>
