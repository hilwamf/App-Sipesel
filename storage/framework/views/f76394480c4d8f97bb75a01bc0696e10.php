<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Edit Profil - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Poppins',sans-serif;} #notifPanel{display:none;}</style>
</head>
<body class="bg-green-50 min-h-screen">

<?php $showNotif = false; ?>
<?php echo $__env->make('layouts.pedagang-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="max-w-2xl mx-auto px-6 py-10">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Profil</h1>
        <p class="text-gray-500 text-sm mt-1">Perbarui data diri Anda</p>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl mb-5 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-5 flex items-center gap-4">
            <?php if($user->foto_profil && file_exists(public_path($user->foto_profil))): ?>
            <img src="<?php echo e(asset($user->foto_profil)); ?>" class="w-14 h-14 rounded-full object-cover border-2 border-yellow-400 flex-shrink-0">
            <?php else: ?>
            <div class="w-14 h-14 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-black text-xl flex-shrink-0"><?php echo e(strtoupper(substr($user->username,0,1))); ?></div>
            <?php endif; ?>
            <div class="text-white">
                <p class="font-bold text-lg"><?php echo e($user->nama); ?></p>
                <p class="text-green-200 text-sm"><?php echo e($user->username); ?> · Kios <?php echo e($user->no_kios ?? '-'); ?></p>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('profil.update')); ?>" class="px-6 py-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo e(old('nama', $user->nama)); ?>" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                <input type="text" value="<?php echo e($user->username); ?>" disabled
                    class="w-full px-4 py-3 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor HP</label>
                <input type="text" name="nomor_hp" value="<?php echo e(old('nomor_hp', $user->nomor_hp)); ?>" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>
            <?php if($user->no_kios): ?>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Kios</label>
                <input type="text" value="<?php echo e($user->no_kios); ?>" disabled
                    class="w-full px-4 py-3 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
            </div>
            <?php endif; ?>

            <hr class="border-gray-100">
            <p class="text-sm font-semibold text-gray-700">Ganti Password <span class="text-gray-400 font-normal text-xs">(kosongkan jika tidak ingin ganti)</span></p>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru</label>
                <input type="password" name="password" placeholder="Min. 6 karakter"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl transition text-sm">Simpan</button>
                <a href="<?php echo e(route('pedagang.dashboard')); ?>" class="flex-1 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl text-center hover:bg-gray-50 transition text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-6">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span>
</footer>
</body>
</html><?php /**PATH C:\xampp\htdocs\Sipesel\resources\views/profile/edit-pedagang.blade.php ENDPATH**/ ?>