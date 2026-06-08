<?php $__env->startSection('title','Manajemen Kios - SIPESEL'); ?>
<?php $__env->startSection('page-title','Manajemen Kios'); ?>
<?php $__env->startSection('page-sub','Kelola data kios Pasar Wadungasri'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?><div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 mb-5 text-sm flex items-center gap-2"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center"><p class="text-2xl font-black text-gray-800"><?php echo e($stat['total']); ?></p><p class="text-xs text-gray-400 mt-1">Total Kios</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-green-100 text-center"><p class="text-2xl font-black text-green-600"><?php echo e($stat['terisi']); ?></p><p class="text-xs text-gray-400 mt-1">Terisi</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-yellow-100 text-center"><p class="text-2xl font-black text-yellow-600"><?php echo e($stat['kosong']); ?></p><p class="text-xs text-gray-400 mt-1">Kosong</p></div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 text-center"><p class="text-2xl font-black text-red-500"><?php echo e($stat['maintenance']); ?></p><p class="text-xs text-gray-400 mt-1">Maintenance</p></div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <div class="flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 flex-1">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari kios..." class="flex-1 min-w-40 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua</option><option value="terisi" <?php echo e(request('status')=='terisi'?'selected':''); ?>>Terisi</option><option value="kosong" <?php echo e(request('status')=='kosong'?'selected':''); ?>>Kosong</option><option value="maintenance" <?php echo e(request('status')=='maintenance'?'selected':''); ?>>Maintenance</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm">Filter</button>
            <a href="<?php echo e(route('admin.kios')); ?>" class="px-4 py-2.5 text-gray-500 text-sm">Reset</a>
        </form>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah Kios
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Kios</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ukuran</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tarif/Bulan</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pedagang</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $kiosList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4"><span class="font-black text-green-700 font-mono bg-green-50 px-2 py-1 rounded-lg text-sm"><?php echo e($k->no_kios); ?></span></td>
                    <td class="px-5 py-4 text-sm text-gray-700"><?php echo e($k->lokasi); ?></td>
                    <td class="px-5 py-4 text-sm text-gray-600"><?php echo e(number_format($k->ukuran,0)); ?> m²</td>
                    <td class="px-5 py-4 text-sm font-semibold">Rp <?php echo e(number_format($k->tarif_bulanan,0,',','.')); ?></td>
                    <td class="px-5 py-4">
                        <?php $pedagang = \App\Models\User::where('role','pedagang')->where('no_kios',$k->no_kios)->first(); ?>
                        <?php if($pedagang): ?>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs flex-shrink-0"><?php echo e(strtoupper(substr($pedagang->username,0,1))); ?></div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700"><?php echo e($pedagang->nama); ?></p>
                                <p class="text-[10px] text-gray-400"><?php echo e($pedagang->username); ?></p>
                            </div>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo e($k->status==='terisi'?'bg-green-100 text-green-700':($k->status==='kosong'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-600')); ?>"><?php echo e(ucfirst($k->status)); ?></span></td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button onclick='editKios(<?php echo json_encode($k, 15, 512) ?>)' class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs">Edit</button>
                            <form method="POST" action="<?php echo e(route('admin.hapus-kios')); ?>" onsubmit="return confirm('Hapus?')"><?php echo csrf_field(); ?><input type="hidden" name="id_kios" value="<?php echo e($k->id_kios); ?>"><button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg text-xs">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada data kios</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="modal-tambah" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Tambah Kios</h3><button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="<?php echo e(route('admin.tambah-kios')); ?>" class="space-y-3"><?php echo csrf_field(); ?>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">No. Kios</label><input type="text" name="no_kios" required placeholder="cth: A-01" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label><input type="text" name="lokasi" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ukuran (m²)</label><input type="number" name="ukuran" step="0.01" value="10" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Tarif/Bulan</label><input type="number" name="tarif_bulanan" value="150000" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-500"><option value="kosong">Kosong</option><option value="terisi">Terisi</option><option value="maintenance">Maintenance</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-green-700 text-white font-semibold rounded-xl text-sm">Simpan</button></div>
        </form>
    </div>
</div>


<div id="modal-edit" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-5"><h3 class="font-bold text-gray-800">Edit Kios</h3><button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">✕</button></div>
        <form method="POST" action="<?php echo e(route('admin.edit-kios')); ?>" class="space-y-3"><?php echo csrf_field(); ?>
            <input type="hidden" name="id_kios" id="edit_id_kios">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">No. Kios</label><input type="text" name="no_kios" id="edit_no_kios" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label><input type="text" name="lokasi" id="edit_lokasi" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ukuran</label><input type="number" name="ukuran" id="edit_ukuran" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Tarif/Bulan</label><input type="number" name="tarif_bulanan" id="edit_tarif" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" id="edit_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"><option value="kosong">Kosong</option><option value="terisi">Terisi</option><option value="maintenance">Maintenance</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label><textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 py-2.5 bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm">Batal</button><button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm">Update</button></div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
function editKios(data){
    document.getElementById('edit_id_kios').value=data.id_kios;
    document.getElementById('edit_no_kios').value=data.no_kios;
    document.getElementById('edit_lokasi').value=data.lokasi;
    document.getElementById('edit_ukuran').value=data.ukuran;
    document.getElementById('edit_tarif').value=data.tarif_bulanan;
    document.getElementById('edit_status').value=data.status;
    document.getElementById('edit_keterangan').value=data.keterangan||'';
    document.getElementById('modal-edit').classList.remove('hidden');
}
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.sidebar-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/admin/kios.blade.php ENDPATH**/ ?>