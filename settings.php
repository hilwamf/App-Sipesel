<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}
require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));
$pesan = '';
$error = '';

// Simpan setting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_setting') {
    $settings_input = $_POST['settings'] ?? [];
    $berhasil = 0;
    foreach ($settings_input as $nama => $nilai) {
        $nama = mysqli_real_escape_string($conn, $nama);
        $nilai = mysqli_real_escape_string($conn, $nilai);
        $sql = "INSERT INTO settings (nama_setting, nilai) VALUES ('$nama', '$nilai')
                ON DUPLICATE KEY UPDATE nilai='$nilai', updated_at=NOW()";
        if (mysqli_query($conn, $sql)) $berhasil++;
    }
    if ($berhasil > 0) $pesan = "Setting berhasil disimpan ($berhasil item).";
    else $error = "Gagal menyimpan setting.";
}

// Tambah setting baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_setting']);
    $nilai = mysqli_real_escape_string($conn, $_POST['nilai']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $sql = "INSERT INTO settings (nama_setting, nilai, deskripsi) VALUES ('$nama', '$nilai', '$deskripsi')";
    if (mysqli_query($conn, $sql)) $pesan = "Setting baru berhasil ditambahkan!";
    else $error = "Gagal: nama setting sudah ada atau error lainnya.";
}

// Hapus setting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'hapus') {
    $id = intval($_POST['id_setting']);
    if (mysqli_query($conn, "DELETE FROM settings WHERE id_setting=$id")) $pesan = "Setting dihapus.";
    else $error = "Gagal hapus.";
}

// Generate tagihan bulan ini
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'generate_tagihan') {
    $bulan = intval(date('m'));
    $tahun = intval(date('Y'));
    $tarif_default = 250000;
    
    // Ambil tarif default dari settings
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM settings WHERE nama_setting='tarif_pajak_default'"));
    if ($r) $tarif_default = intval($r['nilai']);
    
    // Ambil semua pedagang aktif
    $pedagang = mysqli_query($conn, "SELECT id_user FROM users WHERE role='pedagang'");
    $dibuat = 0;
    while ($p = mysqli_fetch_assoc($pedagang)) {
        $sql = "INSERT IGNORE INTO tagihan (id_user, bulan, tahun, nominal, status) 
                VALUES ({$p['id_user']}, $bulan, $tahun, $tarif_default, 'belum_bayar')";
        if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) $dibuat++;
    }
    $pesan = "Berhasil generate $dibuat tagihan baru untuk bulan ".date('F Y').".";
}

// Ambil semua settings
$all_settings = [];
$r = mysqli_query($conn, "SELECT * FROM settings ORDER BY id_setting");
if ($r) while ($row = mysqli_fetch_assoc($r)) $all_settings[] = $row;

// Pending badge
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM transaksi WHERE status='pending'"))['c'];

// Statistik sistem
$sys_stats = [
    'total_user'     => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'],
    'total_kios'     => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM kios"))['c'] ?? 0,
    'total_transaksi'=> mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM transaksi"))['c'],
    'total_pending'  => $total_pending,
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Sistem - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>

<!-- HEADER -->
<header class="bg-green-800 shadow-xl sticky top-0 z-50">
    <div class="border-b border-green-600/50 py-2 px-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <span class="font-extrabold text-yellow-400 text-2xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
            <span class="text-green-300 text-xs font-medium hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar - ADMIN PANEL</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?php echo $inisial; ?></div>
                <span class="text-green-100 text-sm font-medium hidden sm:block"><?php echo htmlspecialchars($username); ?></span>
                <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">ADMIN</span>
            </div>
        </div>
    </div>
    <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
        <a href="Dashboard_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Dashboard</a>
        <a href="verifikasi_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium flex items-center gap-2 whitespace-nowrap">
            Verifikasi
            <?php if ($total_pending > 0): ?><span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?php echo $total_pending; ?></span><?php endif; ?>
        </a>
        <a href="manajemen_user.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Users</a>
        <a href="manajemen_kios.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Kios</a>
        <a href="laporan_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Laporan</a>
        <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Monitoring</a>
        <a href="setting_sistem.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm whitespace-nowrap">Setting</a>
        <div class="ml-auto">
            <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold whitespace-nowrap">Keluar</a>
        </div>
    </nav>
</header>

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-5xl mx-auto">

        <div class="mb-6">
            <h1 class="text-3xl font-bold">Setting Sistem</h1>
            <p class="text-sm opacity-80">Konfigurasi parameter sistem SIPESEL</p>
        </div>

        <!-- Alert -->
        <?php if ($pesan): ?>
        <div class="bg-green-500/30 border border-green-400 text-green-200 px-4 py-3 rounded-lg mb-4">✅ <?php echo $pesan; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="bg-red-500/30 border border-red-400 text-red-200 px-4 py-3 rounded-lg mb-4">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Info Sistem -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
                <p class="text-2xl font-bold"><?php echo $sys_stats['total_user']; ?></p>
                <p class="text-xs opacity-60 mt-1">Total User</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
                <p class="text-2xl font-bold"><?php echo $sys_stats['total_kios']; ?></p>
                <p class="text-xs opacity-60 mt-1">Total Kios</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
                <p class="text-2xl font-bold"><?php echo $sys_stats['total_transaksi']; ?></p>
                <p class="text-xs opacity-60 mt-1">Total Transaksi</p>
            </div>
            <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center">
                <p class="text-2xl font-bold text-yellow-300"><?php echo $sys_stats['total_pending']; ?></p>
                <p class="text-xs opacity-60 mt-1">Pending</p>
            </div>
        </div>

        <!-- Konfigurasi Setting -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Konfigurasi Parameter
                </h2>
                <p class="text-xs opacity-60 mt-1">Edit nilai dan klik Simpan Semua</p>
            </div>
            <?php if (!empty($all_settings)): ?>
            <form method="POST">
                <input type="hidden" name="action" value="save_setting">
                <div class="divide-y divide-white/10">
                    <?php foreach ($all_settings as $s): ?>
                    <div class="px-6 py-4 flex items-center gap-4 flex-wrap">
                        <div class="flex-1 min-w-48">
                            <p class="font-mono text-yellow-300 text-sm"><?php echo htmlspecialchars($s['nama_setting']); ?></p>
                            <p class="text-xs opacity-60 mt-0.5"><?php echo htmlspecialchars($s['deskripsi'] ?? ''); ?></p>
                        </div>
                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <input type="text" name="settings[<?php echo htmlspecialchars($s['nama_setting']); ?>]" 
                                   value="<?php echo htmlspecialchars($s['nilai']); ?>"
                                   class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id_setting" value="<?php echo $s['id_setting']; ?>">
                                <button type="submit" onclick="return confirm('Hapus setting ini?')" 
                                    class="bg-red-500/30 hover:bg-red-500/50 text-red-300 px-2 py-2 rounded-lg text-xs transition-all whitespace-nowrap">Hapus</button>
                            </form>
                        </div>
                        <p class="text-xs opacity-40 w-full md:w-auto">
                            Diperbarui: <?php echo $s['updated_at'] ? date('d M Y H:i', strtotime($s['updated_at'])) : '-'; ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="px-6 py-4 border-t border-white/10">
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold px-6 py-2 rounded-lg transition-all">
                        💾 Simpan Semua Setting
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="px-6 py-8 text-center opacity-50">Belum ada setting. Jalankan SQL setup terlebih dahulu.</div>
            <?php endif; ?>
        </div>

        <!-- Tambah Setting Baru -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="font-bold text-lg">➕ Tambah Setting Baru</h2>
            </div>
            <div class="px-6 py-4">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="hidden" name="action" value="tambah">
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Nama Setting (key)</label>
                        <input type="text" name="nama_setting" required placeholder="cth: max_upload_mb"
                            class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Nilai</label>
                        <input type="text" name="nilai" required placeholder="cth: 5"
                            class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Deskripsi</label>
                        <input type="text" name="deskripsi" placeholder="cth: Ukuran max upload file (MB)"
                            class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                    <div class="md:col-span-3">
                        <button type="submit" class="bg-green-500/80 hover:bg-green-500 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-all">Tambah Setting</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tools / Utilitas -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="font-bold text-lg">🔧 Utilitas Sistem</h2>
                <p class="text-xs opacity-60 mt-1">Alat bantu pengelolaan data</p>
            </div>
            <div class="px-6 py-4 space-y-4">
                
                <!-- Generate Tagihan -->
                <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold">Generate Tagihan Bulanan</h3>
                            <p class="text-xs opacity-60 mt-1">
                                Buat tagihan otomatis untuk semua pedagang aktif di bulan <?php echo date('F Y'); ?>.
                                Hanya membuat tagihan yang belum ada.
                            </p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="generate_tagihan">
                            <button type="submit" onclick="return confirm('Generate tagihan untuk bulan <?php echo date('F Y'); ?>?')"
                                class="bg-blue-500/80 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all">
                                Generate Tagihan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Database -->
                <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                    <h3 class="font-semibold mb-2">Info Aplikasi</h3>
                    <div class="grid grid-cols-2 gap-2 text-xs opacity-70">
                        <span>Versi PHP:</span><span><?php echo phpversion(); ?></span>
                        <span>Server Time:</span><span><?php echo date('d M Y H:i:s'); ?></span>
                        <span>Aplikasi:</span><span>SIPESEL v1.0</span>
                        <span>Database:</span><span><?php echo mysqli_get_server_info($conn); ?></span>
                    </div>
                </div>
                
            </div>
        </div>

    </div>
</section>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.
</footer>
</body>
</html>