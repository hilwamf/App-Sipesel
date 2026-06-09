<?php $__env->startSection('title','Monitoring - SIPESEL Pengawas'); ?>
<?php $__env->startSection('page-title','Monitoring Pembayaran'); ?>
<?php $__env->startSection('page-sub','Pantau semua transaksi pedagang'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Filter Status</label>
            <select onchange="window.location.href='<?php echo e(route('pengawas.monitoring')); ?>?filter='+this.value"
                class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="semua" <?php echo e($filter==='semua'?'selected':''); ?>>Semua Transaksi</option>
                <option value="pending"  <?php echo e($filter==='pending' ?'selected':''); ?>>Pending</option>
                <option value="berhasil" <?php echo e($filter==='berhasil'?'selected':''); ?>>Berhasil</option>
                <option value="gagal"    <?php echo e($filter==='gagal'   ?'selected':''); ?>>Gagal/Ditolak</option>
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
            <input id="searchInput" type="text" placeholder="Cari pedagang / kios..." oninput="filterTable()"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full" id="tabelTransaksi">
            <thead><tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Transaksi</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kios</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pedagang</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="baris-transaksi hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-xs font-mono text-gray-600"><?php echo e($t->no_trx); ?></td>
                    <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg"><?php echo e($t->nomor_kios); ?></span></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo e($t->user->nama ?? '-'); ?><br><span class="text-xs text-gray-400"><?php echo e($t->user->username ?? ''); ?></span></td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium <?php echo e($t->jenis_pajak==='Harian'?'bg-sky-100 text-sky-700':($t->jenis_pajak==='Mingguan'?'bg-purple-100 text-purple-700':'bg-indigo-100 text-indigo-700')); ?>"><?php echo e($t->jenis_pajak); ?></span></td>
                    <td class="px-4 py-3 text-xs text-gray-600"><?php echo e($t->metode_pembayaran); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo e($t->tanggal->format('d/m/Y')); ?></td>
                    <td class="px-4 py-3 text-right font-bold text-sm text-gray-800">Rp <?php echo e(number_format($t->nominal,0,',','.')); ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($t->status==='approved'?'bg-green-100 text-green-700':($t->status==='pending'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-600')); ?>">
                            <?php echo e($t->status==='approved'?'✓ Berhasil':($t->status==='pending'?'⏳ Pending':'✗ Ditolak')); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm">Belum ada data transaksi</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-xs text-gray-400 mb-1">Total Transaksi</p>
        <p class="text-2xl font-black text-gray-800"><?php echo e($transaksi->count()); ?></p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100 text-center">
        <p class="text-xs text-gray-400 mb-1">Total Nominal</p>
        <p class="text-lg font-black text-green-700">Rp <?php echo e(number_format($totalNominal,0,',','.')); ?></p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 text-center">
        <p class="text-xs text-gray-400 mb-1">Rata-rata</p>
        <p class="text-lg font-black text-blue-700">Rp <?php echo e(number_format($rataRata,0,',','.')); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
function filterTable() {
    const kw = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#tabelTransaksi .baris-transaksi').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(kw) ? '' : 'none';
    });
}
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.sidebar-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/pengawas/monitoring.blade.php ENDPATH**/ ?>