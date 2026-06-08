<?php $__env->startSection('title','Setting Sistem - SIPESEL'); ?>
<?php $__env->startSection('page-title','Setting Sistem'); ?>
<?php $__env->startSection('page-sub','Konfigurasi parameter sistem SIPESEL'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?><div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 text-sm flex items-center gap-2"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800"><?php echo e($sysStats['total_user']); ?></p><p class="text-xs text-gray-400 mt-1">Total User</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800"><?php echo e($sysStats['total_kios']); ?></p><p class="text-xs text-gray-400 mt-1">Total Kios</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800"><?php echo e($sysStats['total_transaksi']); ?></p><p class="text-xs text-gray-400 mt-1">Total Transaksi</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-yellow-100 text-center"><p class="text-2xl font-black text-yellow-600"><?php echo e($sysStats['total_pending']); ?></p><p class="text-xs text-gray-400 mt-1">Pending</p></div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
    <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Konfigurasi Parameter</h2><p class="text-xs text-gray-400">Edit nilai lalu klik Simpan Semua</p></div>
    <?php if($settings->count() > 0): ?>
    <form method="POST" action="<?php echo e(route('admin.save-setting')); ?>"><?php echo csrf_field(); ?>
        <div class="divide-y divide-gray-50">
            <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-5 py-3 flex items-center gap-4 flex-wrap">
                <div class="flex-1 min-w-40"><p class="font-mono text-sm font-semibold text-green-700"><?php echo e($s->nama_setting); ?></p><p class="text-xs text-gray-400"><?php echo e($s->deskripsi ?? ''); ?></p></div>
                <div class="flex items-center gap-2">
                    <input type="text" name="settings[<?php echo e($s->nama_setting); ?>]" value="<?php echo e($s->nilai); ?>" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 w-40">
                    <form method="POST" action="<?php echo e(route('admin.hapus-setting')); ?>" class="inline" onsubmit="return confirm('Hapus?')"><?php echo csrf_field(); ?><input type="hidden" name="id_setting" value="<?php echo e($s->id_setting); ?>"><button class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold">Hapus</button></form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100"><button type="submit" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm">💾 Simpan Semua</button></div>
    </form>
    <?php else: ?>
    <div class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada setting.</div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Tambah Setting Baru</h2></div>
        <div class="px-5 py-4">
            <form method="POST" action="<?php echo e(route('admin.tambah-setting')); ?>" class="space-y-3"><?php echo csrf_field(); ?>
                <input type="text" name="nama_setting" required placeholder="Nama setting (key)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="text" name="nilai" required placeholder="Nilai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="text" name="deskripsi" placeholder="Deskripsi (opsional)" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" class="w-full py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm">Tambah Setting</button>
            </form>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"><h2 class="font-bold text-gray-800">Utilitas Sistem</h2></div>
        <div class="px-5 py-4 space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div><p class="font-semibold text-gray-800 text-sm">Generate Tagihan</p><p class="text-xs text-gray-400">Bulan <?php echo e(now()->locale('id')->isoFormat('MMMM YYYY')); ?></p></div>
                <form method="POST" action="<?php echo e(route('admin.generate-tagihan')); ?>"><?php echo csrf_field(); ?><button onclick="return confirm('Generate?')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs">Generate</button></form>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl text-xs text-gray-500 space-y-1">
                <div class="flex justify-between"><span>PHP:</span><span><?php echo e(phpversion()); ?></span></div>
                <div class="flex justify-between"><span>Server Time:</span><span><?php echo e(now()->format('d M Y H:i')); ?></span></div>
                <div class="flex justify-between"><span>App:</span><span>SIPESEL v1.0</span></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.sidebar-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/admin/setting.blade.php ENDPATH**/ ?>