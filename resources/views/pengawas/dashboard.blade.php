@extends('layouts.sidebar-admin')
@section('title','Dashboard Pengawas - SIPESEL')
@section('page-title','Dashboard Pengawas')
@section('page-sub','Selamat datang, ' . auth()->user()->nama)

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
{{-- BANNER --}}
<div class="rounded-2xl mb-6 text-white relative overflow-hidden" style="min-height:130px; background-image:url('{{ asset("images/kios2.jpg") }}'); background-size:cover; background-position:center;">
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
    <div class="relative p-6">
        <p class="text-green-300 text-sm mb-1">Pengawas · SIPESEL</p>
        <h2 class="text-2xl font-black" style="font-family:'Montserrat',sans-serif;">Halo, <span class="text-yellow-400">{{ auth()->user()->nama }}!</span></h2>
        <p class="text-white/70 text-sm mt-1">Monitor kepatuhan pembayaran pajak pedagang.</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">{{ $totalPedagang }}</p>
        <p class="text-xs text-gray-400 mt-1">Total Pedagang</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100 text-center">
        <p class="text-2xl font-black text-green-600" style="font-family:'Montserrat',sans-serif;">{{ $sudahBayar }}</p>
        <p class="text-xs text-gray-400 mt-1">Sudah Bayar</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 text-center">
        <p class="text-2xl font-black text-red-600" style="font-family:'Montserrat',sans-serif;">{{ $jatuhTempo }}</p>
        <p class="text-xs text-gray-400 mt-1">Jatuh Tempo</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 text-center">
        <p class="text-2xl font-black text-blue-600" style="font-family:'Montserrat',sans-serif;">{{ $kepatuhan }}%</p>
        <p class="text-xs text-gray-400 mt-1">Kepatuhan</p>
    </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Line chart: Tren Kepatuhan --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 text-sm mb-1">Tren Kepatuhan Pembayaran</h3>
        <p class="text-xs text-gray-400 mb-4">Persentase pedagang yang bayar per bulan</p>
        <canvas id="chartKepatuhan" height="130"></canvas>
    </div>

    {{-- Donut: Bayar vs Belum bulan ini --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 text-sm mb-1">Status Bulan Ini</h3>
        <p class="text-xs text-gray-400 mb-4">Pedagang sudah & belum bayar</p>
        <canvas id="chartBayar" height="130"></canvas>
    </div>
</div>
@endsection

@section('scripts')
// Line chart kepatuhan
new Chart(document.getElementById('chartKepatuhan').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json(collect($chartKepatuhan)->pluck('bulan')),
        datasets: [{
            label: 'Kepatuhan (%)',
            data: @json(collect($chartKepatuhan)->pluck('kepatuhan')),
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: '#16a34a',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true, max: 100,
                ticks: { callback: v => v+'%', font: { size: 10 } }
            },
            x: { ticks: { font: { size: 10 } } }
        }
    }
});

// Donut bayar vs belum
const lastMonth = @json(end($chartKepatuhan));
new Chart(document.getElementById('chartBayar').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah Bayar', 'Belum Bayar'],
        datasets: [{
            data: [{{ $sudahBayar }}, {{ $totalPedagang - $sudahBayar }}],
            backgroundColor: ['rgba(22,163,74,0.85)', 'rgba(239,68,68,0.85)'],
            borderWidth: 0, hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, cutout: '68%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12, usePointStyle: true } }
        }
    }
});
@endsection