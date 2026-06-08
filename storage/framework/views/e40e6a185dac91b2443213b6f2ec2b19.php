<header class="bg-green-800 shadow-xl sticky top-0 z-50">
    <div class="border-b border-green-600/50 py-2 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
            <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?php echo e(strtoupper(substr($user->username,0,1))); ?></div>
                <span class="text-green-100 text-sm font-medium hidden sm:block"><?php echo e($user->username); ?></span>
            </div>
        </div>
    </div>
    <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
        <a href="<?php echo e(route('pengawas.dashboard')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pengawas.dashboard')?'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold':'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Dashboard</a>
        <a href="<?php echo e(route('pengawas.monitoring')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pengawas.monitoring')?'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold':'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Monitoring</a>
        <a href="<?php echo e(route('pengawas.laporan')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pengawas.laporan')?'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold':'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Laporan</a>
        <div class="ml-auto">
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all">Keluar</button>
            </form>
        </div>
    </nav>
</header>
<?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/layouts/pengawas-header.blade.php ENDPATH**/ ?>