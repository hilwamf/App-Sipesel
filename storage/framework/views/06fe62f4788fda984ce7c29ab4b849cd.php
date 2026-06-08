<?php $totalPending = isset($totalPending) ? $totalPending : \App\Models\Transaksi::where('status','pending')->count(); ?>
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        <span class="font-black text-2xl text-green-700 tracking-tight" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
        <div class="hidden md:flex items-center gap-1">
            <?php
                $navItems = [
                    ['route'=>'admin.dashboard','label'=>'Dashboard'],
                    ['route'=>'admin.verifikasi','label'=>'Verifikasi','badge'=>$totalPending??0],
                    ['route'=>'admin.users','label'=>'Users'],
                    ['route'=>'admin.kios','label'=>'Kios'],
                    ['route'=>'admin.laporan','label'=>'Laporan'],
                    ['route'=>'admin.monitoring','label'=>'Monitoring'],
                    ['route'=>'admin.setting','label'=>'Setting'],
                ];
            ?>
            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($item['route'])); ?>" class="px-4 py-2 rounded-full text-sm font-medium transition-all flex items-center gap-1 <?php echo e(request()->routeIs($item['route']) ? 'bg-green-700 text-white' : 'text-gray-600 hover:bg-green-50 hover:text-green-700'); ?>">
                <?php echo e($item['label']); ?>

                <?php if(isset($item['badge']) && $item['badge'] > 0): ?>
                <span class="px-1.5 py-0.5 bg-red-500 text-white text-[10px] rounded-full"><?php echo e($item['badge']); ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('profil.edit')); ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-200 hover:border-green-400 transition">
                <div class="w-7 h-7 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-xs"><?php echo e(strtoupper(substr($user->username,0,1))); ?></div>
                <span class="text-sm text-gray-700 font-medium hidden sm:block"><?php echo e($user->username); ?></span>
            </a>
            <form action="<?php echo e(route('logout')); ?>" method="POST"><?php echo csrf_field(); ?>
                <button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-full transition">Keluar</button>
            </form>
        </div>
    </div>
</nav><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/layouts/admin-nav.blade.php ENDPATH**/ ?>