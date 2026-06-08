<?php $__env->startSection('title','Dashboard Pengawas - SIPESEL'); ?>
<?php $__env->startSection('page-title','Dashboard Pengawas'); ?>
<?php $__env->startSection('page-sub','Selamat datang, ' . auth()->user()->nama); ?>

<?php $__env->startSection('styles'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="rounded-2xl mb-6 text-white relative overflow-hidden" style="min-height:130px; background-image:url('<?php echo e(asset("images/kios2.jpg")); ?>'); background-size:cover; background-position:center;">
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
    <div class="relative p-6">
        <p class="text-green-300 text-sm mb-1">Pengawas · SIPESEL</p>
        <h2 class="text-2xl font-black" style="font-family:'Montserrat',sans-serif;">Halo, <span class="text-yellow-400"><?php echo e(auth()->user()->nama); ?>!</span></h2>
        <p class="text-white/70 text-sm mt-1">Monitor kepatuhan pembayaran pajak pedagang.</p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-black text-gray-800" style="font-family:'Montserrat',sans-serif;"><?php echo e($totalPedagang); ?></p>
        <p class="text-xs text-gray-400 mt-1">Total Pedagang</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100 text-center">
        <p class="text-2xl font-black text-green-600" style="font-family:'Montserrat',sans-serif;"><?php echo e($sudahBayar); ?></p>
        <p class="text-xs text-gray-400 mt-1">Sudah Bayar</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 text-center">
        <p class="text-2xl font-black text-red-600" style="font-family:'Montserrat',sans-serif;"><?php echo e($jatuhTempo); ?></p>
        <p class="text-xs text-gray-400 mt-1">Jatuh Tempo</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 text-center">
        <p class="text-2xl font-black text-blue-600" style="font-family:'Montserrat',sans-serif;"><?php echo e($kepatuhan); ?>%</p>
        <p class="text-xs text-gray-400 mt-1">Kepatuhan</p>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 text-sm mb-1">Tren Kepatuhan Pembayaran</h3>
        <p class="text-xs text-gray-400 mb-4">Persentase pedagang yang bayar per bulan</p>
        <canvas id="chartKepatuhan" height="130"></canvas>
    </div>

    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 text-sm mb-1">Status Bulan Ini</h3>
        <p class="text-xs text-gray-400 mb-4">Pedagang sudah & belum bayar</p>
        <canvas id="chartBayar" height="130"></canvas>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
// Line chart kepatuhan
new Chart(document.getElementById('chartKepatuhan').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(collect($chartKepatuhan)->pluck('bulan'), 15, 512) ?>,
        datasets: [{
            label: 'Kepatuhan (%)',
            data: <?php echo json_encode(collect($chartKepatuhan)->pluck('kepatuhan'), 15, 512) ?>,
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
const lastMonth = <?php echo json_encode(end($chartKepatuhan), 15, 512) ?>;
new Chart(document.getElementById('chartBayar').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah Bayar', 'Belum Bayar'],
        datasets: [{
            data: [<?php echo e($sudahBayar); ?>, <?php echo e($totalPedagang - $sudahBayar); ?>],
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.sidebar-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/pengawas/dashboard.blade.php ENDPATH**/ ?>