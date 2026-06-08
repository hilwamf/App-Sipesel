<?php $__env->startSection('title','Laporan Pembayaran - SIPESEL'); ?>
<?php $__env->startSection('page-title','Laporan Pembayaran'); ?>
<?php $__env->startSection('page-sub', 'Laporan Pembayaran Pajak'); ?>

<?php $__env->startSection('content'); ?>

<div class="bg-gradient-to-r from-green-800 to-green-600 relative overflow-hidden no-print">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-32 w-52 h-52 bg-yellow-400 rounded-full"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6 py-8 flex items-center justify-between flex-wrap gap-4">
        <div>
            <p class="text-green-300 text-sm mb-1">Admin Panel · SIPESEL</p>
            <h1 class="text-3xl font-black text-white" style="font-family:'Montserrat',sans-serif;">Laporan Pembayaran</h1>
            <p class="text-green-200 text-sm mt-1"><?php echo e($namaBulan[$bulan]); ?> <?php echo e($tahun); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.laporan', array_merge(request()->all(), ['export'=>'csv']))); ?>"
               class="flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold rounded-xl text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Export CSV
            </a>
            <a href="<?php echo e(route('admin.laporan', array_merge(request()->all(), ['export'=>'pdf']))); ?>" target="_blank"
               class="flex items-center gap-2 px-5 py-2.5 bg-red-500/80 hover:bg-red-500 border border-red-400/50 text-white font-semibold rounded-xl text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
            <button onclick="window.print()"
               class="flex items-center gap-2 px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-semibold rounded-xl text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-8">

    
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-6 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Bulan</label>
                <select name="bulan" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <?php $__currentLoopData = $namaBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $nb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($i > 0): ?><option value="<?php echo e($i); ?>" <?php echo e($i==$bulan?'selected':''); ?>><?php echo e($nb); ?></option><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Tahun</label>
                <select name="tahun" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <?php for($y=2024;$y<=2027;$y++): ?><option value="<?php echo e($y); ?>" <?php echo e($y==$tahun?'selected':''); ?>><?php echo e($y); ?></option><?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <option value="">Semua Status</option>
                    <option value="approved" <?php echo e($filterStatus=='approved'?'selected':''); ?>>Approved</option>
                    <option value="pending"  <?php echo e($filterStatus=='pending' ?'selected':''); ?>>Pending</option>
                    <option value="rejected" <?php echo e($filterStatus=='rejected'?'selected':''); ?>>Rejected</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm transition">Tampilkan</button>
        </form>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 mb-1">Total Masuk</p>
            <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">Rp <?php echo e(number_format($ringkasan['total_masuk'],0,',','.')); ?></p>
            <p class="text-xs text-green-600 mt-1"><?php echo e($ringkasan['jml_approved']); ?> transaksi</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 mb-1">Masih Pending</p>
            <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;">Rp <?php echo e(number_format($ringkasan['total_pending'],0,',','.')); ?></p>
            <p class="text-xs text-yellow-600 mt-1"><?php echo e($ringkasan['jml_pending']); ?> transaksi</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 mb-1">Belum Bayar</p>
            <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;"><?php echo e($belumBayar); ?> kios</p>
            <p class="text-xs text-red-500 mt-1">dari <?php echo e($totalPedagang); ?> pedagang</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 card-hover">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-xs text-gray-400 mb-1">Tingkat Kepatuhan</p>
            <p class="text-lg font-black text-gray-800" style="font-family:'Montserrat',sans-serif;"><?php echo e($kepatuhan); ?>%</p>
            <p class="text-xs text-blue-600 mt-1"><?php echo e($sudahBayar); ?>/<?php echo e($totalPedagang); ?> pedagang</p>
        </div>
    </div>

    
    <div class="flex gap-2 mb-4 no-print">
        <button onclick="showTab('transaksi')" id="tab-transaksi"
            class="px-5 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm transition">
            Detail Transaksi
        </button>
        <button onclick="showTab('rekap')" id="tab-rekap"
            class="px-5 py-2.5 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 hover:text-green-700 transition">
            Rekap Per Pedagang
        </button>
    </div>

    
    <div id="panel-transaksi" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Detail Transaksi — <?php echo e($namaBulan[$bulan]); ?> <?php echo e($tahun); ?></h2>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full"><?php echo e($transaksi->count()); ?> data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedagang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kios</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Nominal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 text-sm text-gray-400"><?php echo e($i+1); ?></td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-800 text-sm"><?php echo e($t->user->nama ?? '-'); ?></p>
                            <p class="text-gray-400 text-xs"><span class="text-gray-400">@</span><?php echo e($t->user->username ?? '-'); ?></p>
                        </td>
                        <td class="px-5 py-4"><span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg"><?php echo e($t->nomor_kios ?? '-'); ?></span></td>
                        <td class="px-5 py-4 text-sm text-gray-600"><?php echo e($t->tanggal->format('d M Y')); ?></td>
                        <td class="px-5 py-4 text-right font-bold text-gray-800 text-sm">Rp <?php echo e(number_format($t->nominal,0,',','.')); ?></td>
                        <td class="px-5 py-4 text-sm text-gray-600"><?php echo e($t->metode_pembayaran); ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo e($t->status==='approved'?'bg-green-100 text-green-700':($t->status==='pending'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700')); ?>">
                                <?php echo e(ucfirst($t->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">Tidak ada transaksi periode ini</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($transaksi->count() > 0): ?>
                <tfoot>
                    <tr class="bg-green-50 border-t-2 border-green-200">
                        <td colspan="4" class="px-5 py-3 text-right text-sm font-bold text-gray-700">Total Approved:</td>
                        <td class="px-5 py-3 text-right font-black text-green-700">Rp <?php echo e(number_format($ringkasan['total_masuk'],0,',','.')); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    
    <div id="panel-rekap" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Rekap Per Pedagang — <?php echo e($namaBulan[$bulan]); ?> <?php echo e($tahun); ?></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pedagang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kios</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Bayar</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Bayar</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $rekapPedagang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 text-sm text-gray-400"><?php echo e($i+1); ?></td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-800 text-sm"><?php echo e($p['nama']); ?></p>
                            <p class="text-gray-400 text-xs"><span class="text-gray-400">@</span><?php echo e($p['username']); ?></p>
                        </td>
                        <td class="px-5 py-4"><span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-lg"><?php echo e($p['no_kios'] ?? '-'); ?></span></td>
                        <td class="px-5 py-4 text-right font-bold text-gray-800 text-sm">
                            <?php echo e($p['bayar'] > 0 ? 'Rp '.number_format($p['bayar'],0,',','.') : '-'); ?>

                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-600">
                            <?php echo e($p['last_bayar'] ? \Carbon\Carbon::parse($p['last_bayar'])->format('d M Y') : '-'); ?>

                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php if($p['jml_bayar'] > 0): ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Sudah Bayar</span>
                            <?php else: ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">✗ Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="text-center">
                            <p class="text-[10px] text-gray-400 mb-1 uppercase tracking-wider font-semibold">Reminder</p>
                            <?php if($p['jml_bayar'] == 0): ?>
                            <button onclick="bukaModalNotifAdmin(<?php echo e($p['id_user'] ?? 0); ?>,'<?php echo e(addslashes($p['nama'])); ?>','<?php echo e($p['no_kios'] ?? '-'); ?>')"
                                class="px-3 py-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-semibold rounded-lg text-xs transition border border-yellow-200">
                                🔔 Kirim Notif
                            </button>
                            <?php else: ?>
                            <button disabled class="px-3 py-1.5 bg-gray-50 text-gray-300 font-semibold rounded-lg text-xs cursor-not-allowed border border-gray-100">
                                ✓ Sudah Bayar
                            </button>
                            <?php endif; ?>
                        </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<div id="modalNotifAdmin" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Kirim Notifikasi</h3>
            <button onclick="tutupModalNotifAdmin()" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button>
        </div>
        <p class="text-sm text-gray-500 mb-1">Kepada: <span id="adminNotifNama" class="font-bold text-green-700"></span></p>
        <p class="text-xs text-gray-400 mb-4">Kios: <span id="adminNotifKios"></span></p>
        <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-2">Isi Pesan Notifikasi</label>
            <textarea id="adminNotifPesan" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"></textarea>
        </div>
        <div class="flex gap-3">
            <button onclick="tutupModalNotifAdmin()" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button>
            <button onclick="kirimNotifAdmin()" class="flex-1 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl text-sm">🔔 Kirim</button>
        </div>
    </div>
</div>

<script>
let adminNotifUserId = null;
const adminCsrf = '<?php echo e(csrf_token()); ?>';
const adminNotifUrl = '<?php echo e(route("admin.kirim-notif-laporan")); ?>';

function bukaModalNotifAdmin(idUser, nama, kios) {
    adminNotifUserId = idUser;
    document.getElementById('adminNotifNama').textContent = nama;
    document.getElementById('adminNotifKios').textContent = kios;
    document.getElementById('adminNotifPesan').value =
        `Yth. ${nama},\n\nKami mengingatkan bahwa tagihan pajak kios ${kios} Anda belum dibayar bulan ini.\n\nMohon segera lakukan pembayaran melalui aplikasi SIPESEL.\n\nTerima kasih,\nAdmin SIPESEL`;
    document.getElementById('modalNotifAdmin').classList.remove('hidden');
}

function tutupModalNotifAdmin() {
    document.getElementById('modalNotifAdmin').classList.add('hidden');
    adminNotifUserId = null;
}

function kirimNotifAdmin() {
    if (!adminNotifUserId) return;
    const pesan = document.getElementById('adminNotifPesan').value.trim();
    if (!pesan) { alert('Pesan tidak boleh kosong!'); return; }
    const fd = new FormData();
    fd.append('id_user', adminNotifUserId);
    fd.append('pesan_custom', pesan);
    fd.append('_token', adminCsrf);
    fetch(adminNotifUrl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
        tutupModalNotifAdmin();
        const box = document.getElementById('toastLaporan');
        const el = document.createElement('div');
        el.className = 'px-4 py-3 rounded-xl text-white text-sm font-medium shadow-lg ' + (d.success?'bg-green-600':'bg-red-600');
        el.textContent = d.success ? 'Notifikasi berhasil dikirim!' : 'Gagal mengirim notifikasi';
        box.appendChild(el);
        setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 3000);
    });
}
</script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
function showTab(tab) {
    document.getElementById('panel-transaksi').classList.add('hidden');
    document.getElementById('panel-rekap').classList.add('hidden');
    document.getElementById('tab-transaksi').className = 'px-5 py-2.5 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 hover:text-green-700 transition';
    document.getElementById('tab-rekap').className    = 'px-5 py-2.5 bg-white text-gray-600 font-semibold rounded-xl text-sm border border-gray-200 hover:bg-green-50 hover:text-green-700 transition';
    document.getElementById('panel-'+tab).classList.remove('hidden');
    document.getElementById('tab-'+tab).className = 'px-5 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm transition';
}
</script>

<div id="toastLaporan" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;"></div>
<script>
const csrfAdmin = '<?php echo e(csrf_token()); ?>';

// kirimNotifAdmin handled by modal above

function toast(msg, type) {
    const box = document.getElementById('toastLaporan');
    const el = document.createElement('div');
    el.className = 'px-5 py-3 rounded-xl text-white text-sm font-medium shadow-xl ' + (type==='success'?'bg-green-600':'bg-red-600');
    el.textContent = msg;
    box.appendChild(el);
    setTimeout(()=>{el.style.opacity='0';setTimeout(()=>el.remove(),300);}, 3000);
}
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.sidebar-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/admin/laporan.blade.php ENDPATH**/ ?>