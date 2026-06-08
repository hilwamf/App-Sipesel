@extends('layouts.sidebar-admin')
@section('title', 'Dashboard Admin - SIPESEL')
@section('page-title', 'Dashboard')
@section('page-sub', 'Selamat datang, ' . auth()->user()->nama)

@section('content')
@php
    $totalPendapatan = \App\Models\Transaksi::where('status','approved')->sum('nominal');
    $totalPedagang   = \App\Models\User::where('role','pedagang')->count();
    $totalPending    = \App\Models\Transaksi::where('status','pending')->count();
    $totalKios       = \App\Models\Kios::count();
    $totalKiosTerisi = \App\Models\Kios::where('status','terisi')->count();
    $transaksiTerbaru = \App\Models\Transaksi::with('user')->orderByDesc('created_at')->limit(5)->get();

    $chartData = [];
    for($i=5;$i>=0;$i--){
        $d = now()->subMonths($i);
        $chartData[] = ['bulan'=>$d->locale('id')->isoFormat('MMM'),'pendapatan'=>(int)\App\Models\Transaksi::where('status','approved')->whereMonth('tanggal',$d->month)->whereYear('tanggal',$d->year)->sum('nominal')];
    }
    $statusChart = ['approved'=>\App\Models\Transaksi::where('status','approved')->count(),'pending'=>\App\Models\Transaksi::where('status','pending')->count(),'rejected'=>\App\Models\Transaksi::where('status','rejected')->count()];
@endphp

{{-- HERO BANNER --}}
<div class="rounded-2xl mb-6 text-white relative overflow-hidden" style="min-height:140px; background-image:url('{{ asset("images/kios2.jpg") }}'); background-size:cover; background-position:center;">
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
    <div class="relative p-6">
        <p class="text-green-300 text-sm mb-1">Admin Panel · SIPESEL</p>
        <h2 class="text-2xl font-black" style="font-family:'Montserrat',sans-serif;">Selamat Datang kembali, <span class="text-yellow-400">{{ auth()->user()->nama }}!</span></h2>
        <p class="text-white/70 text-sm mt-1">Berikut ringkasan aktivitas sistem hari ini.</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-medium">Total</span>
        </div>
        <p class="text-xs text-gray-400 mb-1">Pendapatan</p>
        <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">Rp {{ number_format($totalPendapatan,0,',','.') }}</p>
    </div>
    <div class="card-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            @if($totalPending>0)<span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-medium animate-pulse">Alert</span>@endif
        </div>
        <p class="text-xs text-gray-400 mb-1">Menunggu Verifikasi</p>
        <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">{{ $totalPending }}</p>
    </div>
    <div class="card-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mb-1">Total Pedagang</p>
        <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">{{ $totalPedagang }}</p>
    </div>
    <div class="card-stat">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <p class="text-xs text-gray-400 mb-1">Kios Terisi</p>
        <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">{{ $totalKiosTerisi }}/{{ $totalKios }}</p>
    </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2 card-stat">
        <h3 class="font-bold text-gray-800 text-sm mb-4">Pendapatan 6 Bulan Terakhir</h3>
        <canvas id="chartPendapatan" height="120"></canvas>
    </div>
    <div class="card-stat">
        <h3 class="font-bold text-gray-800 text-sm mb-4">Status Transaksi</h3>
        <canvas id="chartStatus" height="120"></canvas>
    </div>
</div>

{{-- TRANSAKSI TERBARU --}}
<div class="card-stat">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
        <a href="{{ route('admin.monitoring') }}" class="text-green-700 text-xs font-semibold hover:underline">Lihat Semua →</a>
    </div>
    @forelse($transaksiTerbaru as $trx)
    <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0
            {{ $trx->status==='approved'?'bg-green-100 text-green-700':($trx->status==='pending'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700') }}">
            {{ strtoupper(substr($trx->user->nama??'?',0,1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate">{{ $trx->user->nama ?? '-' }}</p>
            <p class="text-gray-400 text-xs">Kios {{ $trx->nomor_kios }} · {{ $trx->jenis_pajak }}</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-gray-800 text-sm">Rp {{ number_format($trx->nominal,0,',','.') }}</p>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $trx->status==='approved'?'bg-green-100 text-green-700':($trx->status==='pending'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700') }}">{{ ucfirst($trx->status) }}</span>
        </div>
    </div>
    @empty
    <p class="text-gray-400 text-sm text-center py-8">Belum ada transaksi</p>
    @endforelse
</div>
@endsection

@section('scripts')
new Chart(document.getElementById('chartPendapatan').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json(collect($chartData)->pluck('bulan')),
        datasets: [{ label: 'Pendapatan', data: @json(collect($chartData)->pluck('pendapatan')), backgroundColor: 'rgba(21,128,61,0.8)', borderRadius: 6, borderWidth: 0 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp '+v.toLocaleString('id-ID'), font:{size:9} } }, x: { ticks: { font:{size:10} } } } }
});
new Chart(document.getElementById('chartStatus').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Approved','Pending','Rejected'],
        datasets: [{ data: [{{ $statusChart['approved'] }},{{ $statusChart['pending'] }},{{ $statusChart['rejected'] }}], backgroundColor: ['rgba(21,128,61,0.85)','rgba(234,179,8,0.85)','rgba(239,68,68,0.85)'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { font:{size:10}, padding:10, usePointStyle:true } } } }
});
@endsection