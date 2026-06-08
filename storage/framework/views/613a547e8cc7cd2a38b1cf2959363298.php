<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pajak - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Poppins',sans-serif;}
        .jenis-card{transition:all .25s ease;cursor:pointer;}
        .jenis-card:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(22,163,74,.15);}
        .jenis-card.selected{border-color:#16a34a!important;background:linear-gradient(135deg,#f0fdf4,#dcfce7);box-shadow:0 0 0 3px rgba(22,163,74,.2);}
        .jenis-card.selected .jenis-check{opacity:1;transform:scale(1);}
        .jenis-check{opacity:0;transform:scale(.5);transition:all .2s;}
        .metode-card{transition:all .2s ease;cursor:pointer;}
        .metode-card:hover{border-color:#16a34a;background:#f0fdf4;}
        .metode-card.selected{border-color:#16a34a!important;background:#f0fdf4;box-shadow:0 0 0 3px rgba(22,163,74,.15);}
        @keyframes popIn{0%{transform:scale(.5) rotate(-10deg);opacity:0}70%{transform:scale(1.1) rotate(3deg)}100%{transform:scale(1) rotate(0);opacity:1}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .anim-pop{animation:popIn .6s ease forwards;}
        .anim-fade{animation:fadeUp .5s ease forwards;}
        .delay-1{animation-delay:.3s;opacity:0;}
        .delay-2{animation-delay:.5s;opacity:0;}
        .delay-3{animation-delay:.7s;opacity:0;}
        .overlay{display:none;}
        .overlay.show{display:flex;}
    </style>
</head>
<body class="bg-green-50 text-neutral-900 min-h-screen">
<?php echo $__env->make('layouts.pedagang-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(!$dataSukses): ?>

<form action="<?php echo e(route('pedagang.proses-bayar')); ?>" method="POST" id="formPembayaran">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="jenis_pajak" id="inputJenis" required>
    <input type="hidden" name="nominal"     id="inputNominal" required>
    <input type="hidden" name="metode"      id="inputMetode" required>

    <div class="max-w-3xl mx-auto px-6 py-10">
        <div class="mb-8">
            <h1 class="font-extrabold text-4xl text-neutral-900 mb-1" style="font-family:'Montserrat',sans-serif;">Pembayaran Pajak</h1>
            <p class="text-neutral-500 text-sm">Pilih jenis pajak dan metode pembayaran Anda</p>
        </div>

        
        <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
            <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                Pilih Jenis Pajak
            </h2>
            <p class="text-neutral-400 text-xs mb-6 ml-9">Pilih periode pembayaran pajak kios Anda</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Harian',5000)">
                    <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                    <h3 class="font-bold text-neutral-800 mb-1 mt-3">Harian</h3>
                    <p class="text-neutral-400 text-xs mb-3">Pembayaran per hari</p>
                    <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 5.000</p>
                </div>
                <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Mingguan',35000)">
                    <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                    <div class="w-fit mb-2 px-2 py-0.5 bg-yellow-400 text-yellow-900 text-[10px] font-bold rounded-full uppercase tracking-wider mt-3">Populer</div>
                    <h3 class="font-bold text-neutral-800 mb-1">Mingguan</h3>
                    <p class="text-neutral-400 text-xs mb-3">Pembayaran per minggu</p>
                    <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 35.000</p>
                </div>
                <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Bulanan',140000)">
                    <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                    <div class="w-fit mb-2 px-2 py-0.5 bg-green-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider mt-3">Hemat</div>
                    <h3 class="font-bold text-neutral-800 mb-1">Bulanan</h3>
                    <p class="text-neutral-400 text-xs mb-3">Pembayaran per bulan</p>
                    <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 140.000</p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
            <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                Nomor Kios
            </h2>
            <p class="text-neutral-400 text-xs mb-5 ml-9">Nomor kios Anda</p>
            <input type="text" id="nomorKios" value="<?php echo e($user->no_kios); ?>" readonly
                class="w-full border-2 border-neutral-200 bg-gray-100 rounded-xl px-4 py-3 text-sm cursor-not-allowed">
        </div>

        
        <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
            <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                Metode Pembayaran
            </h2>
            <p class="text-neutral-400 text-xs mb-5 ml-9">Pilih cara pembayaran yang Anda inginkan</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'DANA')">
                    <div class="w-12 h-12 bg-green-100 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="<?php echo e(asset('images/DANA.jpg')); ?>" alt="DANA" class="w-full h-full object-cover">
                    </div>
                    <div><p class="font-semibold text-neutral-800 text-sm">Dana</p><p class="text-neutral-400 text-xs">Bayar menggunakan saldo DANA anda</p></div>
                </div>
                <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'Transfer Bank')">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="<?php echo e(asset('images/bank.jpg')); ?>" alt="Bank" class="w-full h-full object-cover">
                    </div>
                    <div><p class="font-semibold text-neutral-800 text-sm">Transfer Bank</p><p class="text-neutral-400 text-xs">BRI / BNI / Mandiri</p></div>
                </div>
                <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetodeQRIS(this)">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="<?php echo e(asset('images/QRIS.jpg')); ?>" alt="QRIS" class="w-full h-full object-cover">
                    </div>
                    <div><p class="font-semibold text-neutral-800 text-sm">QRIS</p><p class="text-neutral-400 text-xs">Scan QR dari dompet digital</p></div>
                </div>
                <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'Gopay')">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="<?php echo e(asset('images/Gopay.jpg')); ?>" alt="Gopay" class="w-full h-full object-cover">
                    </div>
                    <div><p class="font-semibold text-neutral-800 text-sm">GoPay</p><p class="text-neutral-400 text-xs">Bayar menggunakan saldo Gopay anda</p></div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-yellow-400 mb-8">
            <h2 class="font-bold text-lg text-neutral-800 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-yellow-400 text-yellow-900 text-xs font-bold flex items-center justify-center">✦</span>
                Ringkasan Pembayaran
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm"><span class="text-neutral-500">Nomor Kios</span><span class="font-semibold text-neutral-800" id="sum-kios">-</span></div>
                <div class="flex justify-between text-sm"><span class="text-neutral-500">Jenis Pajak</span><span class="font-semibold text-neutral-800" id="sum-jenis">-</span></div>
                <div class="flex justify-between text-sm"><span class="text-neutral-500">Metode Bayar</span><span class="font-semibold text-neutral-800" id="sum-metode">-</span></div>
                <div class="border-t border-neutral-100 pt-3 flex justify-between items-center">
                    <span class="font-bold text-neutral-800">Total Tagihan</span>
                    <span class="font-extrabold text-2xl text-green-600" id="sum-total" style="font-family:'Montserrat',sans-serif;">Rp 0</span>
                </div>
            </div>
        </div>

        <button type="button" onclick="validasiDanKirim()" class="w-full py-4 text-white font-bold text-lg rounded-2xl hover:shadow-xl hover:shadow-green-200 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3" style="background:linear-gradient(to right,#15803d,#16a34a);">
            <span>Bayar Sekarang</span>
        </button>
        <p class="text-center text-neutral-400 text-xs mt-4">🔒 Transaksi dijamin aman dan terenkripsi</p>
    </div>
</form>


<div id="qrisOverlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center">
    <div class="bg-white rounded-3xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl">
        <h3 class="font-bold text-xl text-neutral-800 mb-1">Scan QRIS</h3>
        <p class="text-neutral-400 text-sm mb-5">Scan QR code menggunakan dompet digital Anda</p>
        <div class="w-48 h-48 mx-auto border-4 border-neutral-800 rounded-2xl flex items-center justify-center mb-5 p-3 bg-white">
            <svg viewBox="0 0 100 100" class="w-full h-full">
                <rect x="5" y="5" width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                <rect x="11" y="11" width="16" height="16" fill="#1f2937"/>
                <rect x="67" y="5" width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                <rect x="73" y="11" width="16" height="16" fill="#1f2937"/>
                <rect x="5" y="67" width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                <rect x="11" y="73" width="16" height="16" fill="#1f2937"/>
                <rect x="40" y="5" width="6" height="6" fill="#1f2937"/>
                <rect x="52" y="5" width="6" height="6" fill="#1f2937"/>
                <rect x="40" y="17" width="6" height="6" fill="#1f2937"/>
                <rect x="52" y="17" width="12" height="6" fill="#1f2937"/>
                <rect x="40" y="40" width="6" height="24" fill="#1f2937"/>
                <rect x="52" y="40" width="6" height="6" fill="#1f2937"/>
                <rect x="64" y="40" width="6" height="12" fill="#1f2937"/>
                <rect x="76" y="40" width="12" height="6" fill="#1f2937"/>
                <rect x="52" y="52" width="12" height="6" fill="#1f2937"/>
                <rect x="76" y="52" width="6" height="18" fill="#1f2937"/>
                <rect x="40" y="70" width="18" height="6" fill="#1f2937"/>
                <rect x="64" y="70" width="6" height="18" fill="#1f2937"/>
                <rect x="52" y="82" width="6" height="12" fill="#1f2937"/>
                <rect x="76" y="76" width="12" height="6" fill="#1f2937"/>
            </svg>
        </div>
        <div class="bg-green-50 rounded-xl p-3 mb-3">
            <p class="text-green-700 text-xs font-medium">Total yang harus dibayar:</p>
            <p class="text-green-600 font-extrabold text-2xl" id="qrisAmount" style="font-family:'Montserrat',sans-serif;">Rp 0</p>
        </div>
        <p class="text-neutral-400 text-xs mb-5">QR berlaku selama <span class="text-green-600 font-semibold" id="qrisTimer">05:00</span></p>
        <div class="flex gap-3">
            <button onclick="tutupQRIS()" class="flex-1 py-2.5 border-2 border-neutral-200 rounded-xl text-sm font-semibold text-neutral-600 hover:border-red-300 hover:text-red-500 transition">Batal</button>
            <button onclick="konfirmasiQRIS()" class="flex-1 py-2.5 text-white rounded-xl text-sm font-semibold transition" style="background:linear-gradient(to right,#15803d,#16a34a);">✓ Sudah Bayar</button>
        </div>
    </div>
</div>

<?php else: ?>

<div class="max-w-lg mx-auto px-6 py-14">
    <div class="text-center mb-8">
        <div class="anim-pop w-28 h-28 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-yellow-300" style="background:linear-gradient(135deg,#facc15,#eab308);">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="anim-fade delay-1 font-extrabold text-3xl text-neutral-900 mt-5" style="font-family:'Montserrat',sans-serif;">Pembayaran Dikirim!</h1>
        <p class="anim-fade delay-2 text-neutral-500 text-sm mt-2">Transaksi Anda sedang menunggu verifikasi dari Admin.</p>
    </div>

    <div class="anim-fade delay-2 bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="p-6 text-center" style="background:linear-gradient(135deg,#166534,#15803d);">
            <p class="text-green-300 text-xs tracking-widest uppercase mb-1">Bukti Pembayaran</p>
            <p class="text-yellow-400 font-extrabold text-xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</p>
            <p class="text-green-300 text-xs mt-1"><?php echo e($dataSukses['tanggal']); ?></p>
        </div>
        <div class="flex items-center px-4"><div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -ml-2.5"></div><div class="flex-1 border-t-2 border-dashed border-neutral-200 mx-2"></div><div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -mr-2.5"></div></div>
        <div class="px-7 py-5 space-y-3.5">
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">No. Transaksi</span><span class="font-semibold text-neutral-700 font-mono text-xs"><?php echo e($dataSukses['no_trx']); ?></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Nama Pemilik</span><span class="font-semibold text-neutral-800"><?php echo e($user->username); ?></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Nomor Kios</span><span class="font-semibold text-neutral-800"><?php echo e($dataSukses['kios']); ?></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Jenis Pajak</span><span class="font-semibold text-neutral-800">Pajak <?php echo e($dataSukses['jenis']); ?></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Metode Bayar</span><span class="font-semibold text-neutral-800"><?php echo e($dataSukses['metode']); ?></span></div>
            <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Status</span>
                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">⏳ Menunggu Verifikasi</span>
            </div>
        </div>
        <div class="flex items-center px-4"><div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -ml-2.5"></div><div class="flex-1 border-t-2 border-dashed border-neutral-200 mx-2"></div><div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -mr-2.5"></div></div>
        <div class="px-7 py-5 bg-green-50 text-center">
            <p class="text-neutral-500 text-xs mb-1">Total Dibayarkan</p>
            <p class="font-extrabold text-4xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp <?php echo e(number_format($dataSukses['nominal'], 0, ',', '.')); ?></p>
        </div>
    </div>

    <div class="anim-fade delay-3 grid grid-cols-2 gap-3 mt-6">
        <a href="<?php echo e(route('pedagang.riwayat')); ?>" class="py-3.5 border-2 border-green-600 text-green-700 rounded-2xl font-semibold text-sm text-center hover:bg-green-600 hover:text-white transition-all duration-300">Riwayat Pembayaran</a>
        <a href="<?php echo e(route('pedagang.pembayaran')); ?>" class="py-3.5 bg-white border-2 border-neutral-200 text-neutral-700 rounded-2xl text-center font-semibold text-sm hover:border-green-500 hover:text-green-600 transition-all duration-300">Bayar Lagi</a>
    </div>
</div>
<?php endif; ?>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar.
</footer>

<script>
let selectedJenis=null,selectedNominal=0,selectedMetode=null,qrisInterval=null,qrisMetodeEl=null;
window.onload=function(){updateRingkasan();};
function pilihJenis(el,jenis,nominal){
    document.querySelectorAll('.jenis-card').forEach(c=>c.classList.remove('selected'));
    el.classList.add('selected');selectedJenis=jenis;selectedNominal=nominal;
    document.getElementById('inputJenis').value=jenis;document.getElementById('inputNominal').value=nominal;
    updateRingkasan();
}
function pilihMetode(el,metode){
    document.querySelectorAll('.metode-card').forEach(c=>c.classList.remove('selected'));
    el.classList.add('selected');selectedMetode=metode;document.getElementById('inputMetode').value=metode;updateRingkasan();
}
function pilihMetodeQRIS(el){
    if(!selectedNominal){alert('Pilih jenis pajak terlebih dahulu!');return;}
    qrisMetodeEl=el;document.getElementById('qrisAmount').innerText='Rp '+selectedNominal.toLocaleString('id-ID');
    document.getElementById('qrisOverlay').classList.add('show');startQrisTimer(300);
}
function tutupQRIS(){document.getElementById('qrisOverlay').classList.remove('show');clearInterval(qrisInterval);}
function konfirmasiQRIS(){tutupQRIS();pilihMetode(qrisMetodeEl,'QRIS');}
function startQrisTimer(seconds){
    clearInterval(qrisInterval);let rem=seconds;
    const tick=()=>{
        const m=String(Math.floor(rem/60)).padStart(2,'0'),s=String(rem%60).padStart(2,'0');
        document.getElementById('qrisTimer').innerText=`${m}:${s}`;
        if(rem<=0)clearInterval(qrisInterval);rem--;
    };tick();qrisInterval=setInterval(tick,1000);
}
function updateRingkasan(){
    const kios=document.getElementById('nomorKios')?.value||'-';
    document.getElementById('sum-kios').innerText=kios;
    document.getElementById('sum-jenis').innerText=selectedJenis||'-';
    document.getElementById('sum-metode').innerText=selectedMetode||'-';
    document.getElementById('sum-total').innerText=selectedNominal?'Rp '+selectedNominal.toLocaleString('id-ID'):'Rp 0';
}
function validasiDanKirim(){
    const kios=document.getElementById('nomorKios')?.value?.trim();
    if(!kios){alert('Nomor kios tidak ditemukan!');return;}
    if(!selectedJenis){alert('Pilih jenis pajak terlebih dahulu!');return;}
    if(!selectedMetode){alert('Pilih metode pembayaran terlebih dahulu!');return;}
    document.getElementById('formPembayaran').submit();
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/pedagang/pembayaran.blade.php ENDPATH**/ ?>