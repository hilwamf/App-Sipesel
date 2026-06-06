<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Dashboard Pengawas - SIPESEL</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body style="background-image:url('{{ asset('images/kios2.jpg') }}');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/30"></div>
@include('layouts.pengawas-header')

<section class="relative z-10 px-6 md:px-20 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="mb-10">
            <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Dashboard Pengawas</h1>
            <h2 class="text-lg mb-4 text-yellow-400">Halo, {{ $user->username }} 👋</h2>
            <p class="text-sm md:text-lg opacity-90 mb-8">Anda masuk sebagai <span class="font-bold text-yellow-300">Pengawas</span></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('pengawas.monitoring') }}" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 flex items-center gap-4 hover:bg-white/15 transition-all hover:-translate-y-0.5">
                <div class="w-14 h-14 bg-blue-500/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-lg">Monitoring</p>
                    <p class="text-sm text-white/60">Pantau seluruh transaksi pembayaran</p>
                </div>
            </a>
            <a href="{{ route('pengawas.laporan') }}" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 flex items-center gap-4 hover:bg-white/15 transition-all hover:-translate-y-0.5">
                <div class="w-14 h-14 bg-yellow-500/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-lg">Laporan</p>
                    <p class="text-sm text-white/60">Status tagihan & jatuh tempo pedagang</p>
                </div>
            </a>
        </div>
    </div>
</section>
</body>
</html>
