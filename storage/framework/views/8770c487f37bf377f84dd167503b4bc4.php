<header class="bg-green-800 shadow-xl sticky top-0 z-50">
    <div class="border-b border-green-600/50 py-2 px-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
            <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
            <div class="flex items-center gap-3">
                <?php if(isset($showNotif) && $showNotif): ?>
                <div class="relative">
                    <button id="notifBtn" class="relative p-2 rounded-lg hover:bg-white/10 transition-all" title="Notifikasi">
                        <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <?php if(isset($unread) && $unread > 0): ?>
                        <span id="notifBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                            <?php echo e($unread > 9 ? '9+' : $unread); ?>

                        </span>
                        <?php endif; ?>
                    </button>
                </div>
                <?php endif; ?>

                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">
                    <?php echo e(strtoupper(substr($user->username, 0, 1))); ?>

                </div>
                <a href="<?php echo e(route('profil.edit')); ?>" class="text-green-100 text-sm font-medium hidden sm:block hover:text-yellow-300 transition"><?php echo e($user->username); ?></a>
            </div>
        </div>
    </div>
    <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
        <a href="<?php echo e(route('pedagang.dashboard')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pedagang.dashboard') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Dashboard</a>
        <a href="<?php echo e(route('pedagang.pembayaran')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pedagang.pembayaran') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Pembayaran</a>
        <a href="<?php echo e(route('pedagang.riwayat')); ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?php echo e(request()->routeIs('pedagang.riwayat') ? 'bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold' : 'text-green-200 hover:bg-green-600/50 hover:text-white'); ?>">Riwayat Bayar</a>
        <div class="ml-auto">
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all">Keluar</button>
            </form>
        </div>
    </nav>
</header>


<?php if(isset($showNotif) && $showNotif): ?>
<div id="notifPanel" style="display:none;position:fixed;top:80px;right:24px;z-index:99999;width:320px;" class="bg-green-900 border border-white/20 rounded-xl shadow-2xl overflow-hidden">
    <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between bg-green-800">
        <p class="font-semibold text-sm text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notifikasi
            <?php if(isset($unread) && $unread > 0): ?>
            <span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?php echo e($unread); ?></span>
            <?php endif; ?>
        </p>
        <button id="notifClose" class="text-white/50 hover:text-white text-xs px-2">✕ Tutup</button>
    </div>
    <?php if(isset($notifikasi) && $notifikasi->count() > 0): ?>
    <div style="max-height:280px;overflow-y:auto;">
        <?php $__currentLoopData = $notifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-all <?php echo e(!$n->dibaca ? 'bg-yellow-500/10 border-l-2 border-l-yellow-400' : ''); ?>">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-white/90 leading-relaxed"><?php echo e($n->pesan); ?></p>
                    <p class="text-xs text-white/40 mt-1"><?php echo e($n->created_at->format('d M Y, H:i')); ?></p>
                </div>
                <?php if(!$n->dibaca): ?>
                <div class="w-2 h-2 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="px-4 py-8 text-center">
        <svg class="w-10 h-10 text-white/20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <p class="text-white/40 text-sm">Tidak ada notifikasi</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
(function() {
    const btn   = document.getElementById('notifBtn');
    const panel = document.getElementById('notifPanel');
    const close = document.getElementById('notifClose');
    const badge = document.getElementById('notifBadge');

    if (!btn || !panel) return;

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = panel.style.display !== 'none';
        panel.style.display = isOpen ? 'none' : 'block';

        if (!isOpen && badge) {
            // Mark as read
            fetch('<?php echo e(route("pedagang.mark-notif-read")); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(d => {
                if (d.success && badge) badge.style.display = 'none';
            }).catch(() => {});
        }
    });

    if (close) {
        close.addEventListener('click', function(e) {
            e.stopPropagation();
            panel.style.display = 'none';
        });
    }

    document.addEventListener('click', function(e) {
        if (panel.style.display !== 'none' && !panel.contains(e.target) && !btn.contains(e.target)) {
            panel.style.display = 'none';
        }
    });
})();
</script><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/layouts/pedagang-header.blade.php ENDPATH**/ ?>