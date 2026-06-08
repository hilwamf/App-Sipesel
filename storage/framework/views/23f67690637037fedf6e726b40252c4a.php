<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #0d3320 0%, #145a32 40%, #1a7a45 70%, #e07b30 100%); }
        .glass-card { background: rgba(255,255,255,0.12); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.2); }
        .feature-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15); transition: all .25s; }
        #notifPanel { display:none; }
        #notifPanel.show { display:block; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float-anim { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen hero-gradient">

<?php $showNotif = true; ?>
<?php echo $__env->make('layouts.pedagang-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="max-w-6xl mx-auto px-6 py-12 w-full">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-10">

        
        <div class="flex flex-col items-center justify-center">
            
            <div class="glass-card rounded-3xl p-8 text-center w-full max-w-sm float-anim">
                
                <div class="relative inline-block mb-4">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-yellow-400 shadow-xl mx-auto">
                        <?php if($user->foto_profil && file_exists(public_path($user->foto_profil))): ?>
                            <img src="<?php echo e(asset($user->foto_profil)); ?>" alt="Foto Profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-green-600 flex items-center justify-center">
                                <span class="text-4xl font-black text-yellow-400" style="font-family:'Montserrat',sans-serif;"><?php echo e(strtoupper(substr($user->nama,0,1))); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-400 border-2 border-white/30 rounded-full"></div>
                </div>

                
                <p class="text-green-300 text-sm mb-1">Halo 👋</p>
                <h2 class="text-white font-black text-2xl mb-1" style="font-family:'Montserrat',sans-serif;"><?php echo e($user->username); ?></h2>
                <p class="text-white/50 text-xs mb-5">Kios <span class="text-yellow-400 font-bold"><?php echo e($user->no_kios ?? '-'); ?></span> · Pasar Wadungasri</p>

                
                <div class="border-t border-white/10 pt-4 space-y-2">
                    <?php if($jatuhTempoStr): ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/50 text-xs">Tagihan Berikutnya</span>
                        <span class="text-white font-bold text-xs"><?php echo e($jatuhTempoStr); ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/50 text-xs">Sisa Waktu</span>
                        <?php if($sudahLewat): ?>
                            <span class="font-bold text-xs text-red-300">⚠ Terlambat <?php echo e($sisaHari); ?> hari</span>
                        <?php elseif($showCountdown): ?>
                            <span class="font-bold text-xs text-yellow-300" id="countdownText">⏱ Menghitung...</span>
                        <?php else: ?>
                            <span class="font-bold text-xs text-green-300">✓ <?php echo e($sisaHari); ?> hari lagi</span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-white/30 text-xs">Belum ada transaksi</p>
                    <?php endif; ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/50 text-xs">Bayar Bulan Ini</span>
                        <span class="text-white font-bold text-xs">
                            <?php echo e($totalBulanIni > 0 ? 'Rp '.number_format($totalBulanIni,0,',','.') : '—'); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="text-white">
            <span class="inline-flex items-center gap-2 glass-card text-green-200 text-xs font-medium px-4 py-2 rounded-full mb-6">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Pasar Wadungasri · Sidoarjo
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-4" style="font-family:'Montserrat',sans-serif;">
                Bayar Pajak<br><span class="text-yellow-400">Lebih Mudah.</span>
            </h1>
            <p class="text-green-200 text-base leading-relaxed mb-6 max-w-md">
                Kelola pembayaran pajak kios Anda secara digital. Cepat, aman, dan bisa dipantau kapan saja melalui platform SIPESEL.
            </p>

            
            <div class="glass-card rounded-2xl px-5 py-4 mb-6 grid grid-cols-3 gap-3">
                <div class="text-center">
                    <p class="text-green-300 text-[10px] uppercase tracking-wider mb-1">Kios</p>
                    <p class="text-white font-black text-lg" style="font-family:'Montserrat',sans-serif;"><?php echo e($user->no_kios ?? '-'); ?></p>
                </div>
                <div class="text-center border-x border-white/10">
                    <p class="text-green-300 text-[10px] uppercase tracking-wider mb-1">Bayar Bulan Ini</p>
                    <p class="text-white font-black text-sm">
                        <?php echo e($totalBulanIni > 0 ? 'Rp '.number_format($totalBulanIni/1000,0,',','.').'K' : '-'); ?>

                    </p>
                </div>
                <div class="text-center">
                    <p class="text-green-300 text-[10px] uppercase tracking-wider mb-1">Status</p>
                    <p class="text-[11px] font-bold <?php echo e($sudahLewat ? 'text-red-300' : 'text-green-300'); ?>">
                        <?php echo e($sudahLewat ? '⚠ Jatuh Tempo' : ($jatuhTempoStr ? '✓ Aktif' : '— Belum Ada')); ?>

                    </p>
                </div>
            </div>

            <div class="flex gap-3 flex-wrap">
                <a href="<?php echo e(route('pedagang.pembayaran')); ?>" class="px-7 py-3.5 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold rounded-full transition-all hover:-translate-y-0.5 shadow-lg shadow-yellow-400/30 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Bayar Pajak
                </a>
                <a href="<?php echo e(route('pedagang.riwayat')); ?>" class="px-7 py-3.5 glass-card hover:bg-white/20 text-white font-semibold rounded-full transition-all text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Bayar
                </a>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        
        <div>
            <?php if(isset($beritaList) && $beritaList->count() > 0): ?>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-white font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Berita & Info Pajak
                </h2>
                <span class="text-green-300 text-xs"><?php echo e($beritaList->count()); ?> berita</span>
            </div>
            <div class="relative rounded-3xl overflow-hidden" style="height:340px;">
                <div id="beritaTrack" class="flex h-full transition-transform duration-700 ease-in-out">
                    <?php $__currentLoopData = $beritaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="min-w-full h-full relative flex-shrink-0">
                        <?php if($b->thumbnail && file_exists(public_path($b->thumbnail))): ?>
                        <img src="<?php echo e(asset($b->thumbnail)); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                        <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-green-800 to-green-600"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <?php endif; ?>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <span class="inline-block px-2.5 py-1 bg-yellow-400/30 border border-yellow-400/40 text-yellow-300 text-[10px] font-bold rounded-full mb-2 uppercase tracking-wider">Info Pajak</span>
                            <h3 class="text-white font-bold text-sm mb-1.5 leading-snug"><?php echo e($b->judul); ?></h3>
                            <p class="text-green-200 text-xs leading-relaxed line-clamp-2"><?php echo e($b->isi); ?></p>
                            <p class="text-green-400/60 text-[10px] mt-2"><?php echo e($b->created_at->locale('id')->isoFormat('D MMMM YYYY')); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($beritaList->count() > 1): ?>
                <button onclick="prevBerita()" class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/30 hover:bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="nextBerita()" class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/30 hover:bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute bottom-3 right-4 flex gap-1.5 z-10" id="beritaDots">
                    <?php $__currentLoopData = $beritaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button onclick="goBerita(<?php echo e($i); ?>)" class="berita-dot transition-all duration-300 rounded-full bg-white/40 h-2 <?php echo e($i===0?'w-6 bg-white/90':'w-2'); ?>"></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="glass-card rounded-3xl flex items-center justify-center text-center p-8" style="height:340px;">
                <div>
                    <svg class="w-12 h-12 text-green-300/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <p class="text-green-300 text-sm">Belum ada berita</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-white font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Transaksi Terbaru
                </h2>
                <a href="<?php echo e(route('pedagang.riwayat')); ?>" class="text-yellow-400 text-xs font-semibold hover:text-yellow-300 transition">Lihat Semua →</a>
            </div>
            <div class="glass-card rounded-3xl overflow-hidden" style="height:340px;">
                <?php if($transaksiTerbaru->count() > 0): ?>
                <div class="h-full overflow-y-auto divide-y divide-white/10">
                    <?php $__currentLoopData = $transaksiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $st    = $trx->status;
                        $badge = $st==='approved' ? ['bg'=>'bg-green-500/20','text'=>'text-green-300','label'=>'✓ Approved']
                               : ($st==='pending'  ? ['bg'=>'bg-yellow-500/20','text'=>'text-yellow-300','label'=>'⏳ Pending']
                               :                    ['bg'=>'bg-red-500/20','text'=>'text-red-300','label'=>'✗ Ditolak']);
                        $icons = ['DANA'=>'💳','Transfer Bank'=>'🏦','QRIS'=>'📱','Gopay'=>'👛'];
                        $icon  = $icons[$trx->metode_pembayaran] ?? '💳';
                    ?>
                    <div class="px-5 py-4 flex items-center gap-4 hover:bg-white/5 transition-all">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"><?php echo e($icon); ?></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-semibold text-sm">Pajak <?php echo e($trx->jenis_pajak); ?></p>
                            <p class="text-green-300 text-xs mt-0.5"><?php echo e($trx->tanggal->format('d M Y, H:i')); ?></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-white font-bold text-sm">Rp <?php echo e(number_format($trx->nominal,0,',','.')); ?></p>
                            <span class="inline-block px-2 py-0.5 <?php echo e($badge['bg']); ?> <?php echo e($badge['text']); ?> rounded-full text-[10px] font-semibold mt-1"><?php echo e($badge['label']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="h-full flex items-center justify-center text-center p-8">
                    <div>
                        <svg class="w-12 h-12 text-green-300/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-green-300 text-sm">Belum ada transaksi</p>
                        <a href="<?php echo e(route('pedagang.pembayaran')); ?>" class="inline-block mt-3 px-5 py-2 bg-yellow-400 text-green-900 font-semibold rounded-full text-xs hover:bg-yellow-300 transition">Bayar Sekarang</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar Wadungasri
</footer>

<script>
// Notifikasi dikelola oleh pedagang-header.blade.php

// Carousel
let beritaIdx = 0;
const beritaTrack = document.getElementById('beritaTrack');
const beritaDots  = document.querySelectorAll('.berita-dot');
const beritaTotal = beritaDots.length;

function goBerita(n) {
    beritaIdx = n;
    if(beritaTrack) beritaTrack.style.transform = `translateX(-${n * 100}%)`;
    beritaDots.forEach((d, i) => {
        d.classList.toggle('w-6', i === n);
        d.classList.toggle('w-2', i !== n);
        d.classList.toggle('bg-white/90', i === n);
        d.classList.toggle('bg-white/40', i !== n);
    });
}
function nextBerita() { goBerita((beritaIdx + 1) % Math.max(beritaTotal, 1)); }
function prevBerita() { goBerita((beritaIdx - 1 + Math.max(beritaTotal, 1)) % Math.max(beritaTotal, 1)); }
if(beritaTotal > 1) setInterval(nextBerita, 5000);

// Countdown timer (jika jatuh tempo kurang dari 24 jam)
(function() {
    const el = document.getElementById('countdownText');
    if (!el) return; // tidak ada countdown jika sisaHari > 0

    <?php if(isset($jatuhTempoDatetime) && $jatuhTempoDatetime): ?>
    const target = new Date('<?php echo e($jatuhTempoDatetime); ?>');
    function update() {
        const now = new Date();
        const diff = target - now;
        if (diff <= 0) {
            el.textContent = '⚠ Sudah Jatuh Tempo!';
            el.style.color = '#fca5a5'; // red-300
            return;
        }
        const h = String(Math.floor(diff / 3600000)).padStart(2,'0');
        const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
        const s = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
        el.textContent = `⏱ ${h}:${m}:${s} lagi`;
    }
    update();
    setInterval(update, 1000);
    <?php endif; ?>
})();

let touchStartX = 0;
if(beritaTrack) {
    beritaTrack.addEventListener('touchstart', e => touchStartX = e.touches[0].clientX);
    beritaTrack.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if(Math.abs(diff) > 50) diff > 0 ? nextBerita() : prevBerita();
    });
}
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/pedagang/dashboard.blade.php ENDPATH**/ ?>