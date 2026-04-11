<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}
require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));

// Filter
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filter_bulan  = isset($_GET['bulan']) ? intval($_GET['bulan']) : 0;
$search        = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$page          = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page      = 15;
$offset        = ($page - 1) * $per_page;

$where = "WHERE 1=1";
if ($filter_status) $where .= " AND t.status = '$filter_status'";
if ($filter_bulan)  $where .= " AND MONTH(t.tanggal) = $filter_bulan";
if ($search)        $where .= " AND (u.nama LIKE '%$search%' OR u.username LIKE '%$search%' OR t.nomor_kios LIKE '%$search%')";

// Total rows untuk pagination
$total_rows = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as c FROM transaksi t JOIN users u ON t.id_user=u.id_user $where"))['c'];
$total_pages = ceil($total_rows / $per_page);

// Data transaksi
$query = "SELECT t.*, u.nama, u.username, u.no_kios as kios_user
          FROM transaksi t
          JOIN users u ON t.id_user = u.id_user
          $where
          ORDER BY t.created_at DESC
          LIMIT $per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
$transaksi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $transaksi[] = $row;
}

// Statistik total sistem
$stats = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT 
        COUNT(*) as total,
        SUM(status='approved') as approved,
        SUM(status='pending') as pending,
        SUM(status='rejected') as rejected,
        COALESCE(SUM(CASE WHEN status='approved' THEN nominal END), 0) as total_pendapatan
     FROM transaksi"));

// Pedagang belum bayar bulan ini
$bulan_ini = intval(date('m'));
$tahun_ini  = intval(date('Y'));
$belum_bayar = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(DISTINCT u.id_user) as c 
     FROM users u
     WHERE u.role = 'pedagang'
     AND u.id_user NOT IN (
         SELECT DISTINCT id_user FROM transaksi 
         WHERE status='approved' AND MONTH(tanggal)=$bulan_ini AND YEAR(tanggal)=$tahun_ini
     )"))['c'];

$total_pedagang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='pedagang'"))['c'];
$tingkat_kepatuhan = $total_pedagang > 0 ? round(($total_pedagang - $belum_bayar) / $total_pedagang * 100) : 0;

// Pending badge
$total_pending = $stats['pending'];

$nama_bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring - SIPESEL</title>
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
        <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm whitespace-nowrap">Monitoring</a>
        <a href="setting_sistem.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Setting</a>
        <div class="ml-auto">
            <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold whitespace-nowrap">Keluar</a>
        </div>
    </nav>
</header>

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Monitoring Transaksi</h1>
            <p class="text-sm opacity-80">Pantau seluruh aktivitas pembayaran sistem</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="col-span-2 bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30">
                <p class="text-xs opacity-70">Total Pendapatan</p>
                <p class="text-xl font-bold text-green-300 mt-1">Rp <?php echo number_format($stats['total_pendapatan'], 0, ',', '.'); ?></p>
                <p class="text-xs opacity-50 mt-1">All time approved</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
                <p class="text-2xl font-bold"><?php echo $stats['total']; ?></p>
                <p class="text-xs opacity-60 mt-1">Total Transaksi</p>
            </div>
            <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center">
                <p class="text-2xl font-bold text-yellow-300"><?php echo $stats['pending']; ?></p>
                <p class="text-xs opacity-60 mt-1">Pending</p>
            </div>
            <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30 text-center">
                <p class="text-2xl font-bold text-red-300"><?php echo $belum_bayar; ?></p>
                <p class="text-xs opacity-60 mt-1">Kios Blm Bayar</p>
            </div>
            <div class="bg-blue-500/20 backdrop-blur-md rounded-xl p-4 border border-blue-400/30 text-center">
                <p class="text-2xl font-bold text-blue-300"><?php echo $tingkat_kepatuhan; ?>%</p>
                <p class="text-xs opacity-60 mt-1">Kepatuhan</p>
            </div>
        </div>

        <!-- Info Tingkat Kepatuhan -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold">Tingkat Kepatuhan Bulan <?php echo $nama_bulan[$bulan_ini]; ?></span>
                <span class="text-sm font-bold <?php echo $tingkat_kepatuhan >= 80 ? 'text-green-300' : ($tingkat_kepatuhan >= 50 ? 'text-yellow-300' : 'text-red-300'); ?>">
                    <?php echo $tingkat_kepatuhan; ?>%
                </span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700
                    <?php echo $tingkat_kepatuhan >= 80 ? 'bg-green-400' : ($tingkat_kepatuhan >= 50 ? 'bg-yellow-400' : 'bg-red-400'); ?>"
                    style="width: <?php echo $tingkat_kepatuhan; ?>%">
                </div>
            </div>
            <p class="text-xs opacity-60 mt-2">
                <?php echo $total_pedagang - $belum_bayar; ?> dari <?php echo $total_pedagang; ?> pedagang sudah membayar bulan ini.
                <?php if ($belum_bayar > 0): ?>
                <span class="text-red-300"><?php echo $belum_bayar; ?> kios belum bayar.</span>
                <?php endif; ?>
            </p>
            <p class="text-xs opacity-50 mt-1 italic">
                💡 Tingkat kepatuhan = persentase pedagang yang sudah membayar tepat waktu di bulan berjalan
            </p>
        </div>

        <!-- Filter -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Cari nama / username / kios..."
                    class="bg-white/10 border border-white/30 rounded-lg px-4 py-2 text-sm text-white placeholder-white/50 focus:outline-none focus:border-yellow-400 flex-1 min-w-48">
                <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    <option value="">Semua Status</option>
                    <option value="approved" <?php echo $filter_status=='approved'?'selected':''; ?>>Approved</option>
                    <option value="pending" <?php echo $filter_status=='pending'?'selected':''; ?>>Pending</option>
                    <option value="rejected" <?php echo $filter_status=='rejected'?'selected':''; ?>>Rejected</option>
                </select>
                <select name="bulan" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    <option value="">Semua Bulan</option>
                    <?php for ($i=1; $i<=12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $filter_bulan==$i?'selected':''; ?>><?php echo $nama_bulan[$i]; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Filter</button>
                <a href="monitoring_admin.php" class="text-green-300 hover:text-white text-sm">Reset</a>
                <span class="text-xs opacity-50 ml-auto"><?php echo $total_rows; ?> data ditemukan</span>
            </form>
        </div>

        <!-- Tabel Monitoring -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-green-800/60 text-left">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Pedagang</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No Kios</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-right">Nominal</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Metode</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Periode</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php if (empty($transaksi)): ?>
                        <tr><td colspan="8" class="px-4 py-8 text-center opacity-50">Tidak ada data</td></tr>
                        <?php endif; ?>
                        <?php foreach ($transaksi as $t): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-xs"><?php echo date('d M Y', strtotime($t['tanggal'])); ?></div>
                                <div class="text-xs opacity-50"><?php echo date('H:i', strtotime($t['created_at'])); ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium"><?php echo htmlspecialchars($t['nama']); ?></div>
                                <div class="text-xs opacity-60"><?php echo htmlspecialchars($t['username']); ?></div>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-yellow-300 text-sm">
                                <?php echo htmlspecialchars($t['nomor_kios'] ?? $t['kios_user'] ?? '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-right font-mono">
                                Rp <?php echo number_format($t['nominal'], 0, ',', '.'); ?>
                            </td>
                            <td class="px-4 py-3 text-xs opacity-80">
                                <?php echo ucwords(str_replace('_', ' ', $t['metode_pembayaran'])); ?>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <?php 
                                $bln = isset($t['bulan_bayar']) ? $t['bulan_bayar'] : intval(date('m', strtotime($t['tanggal'])));
                                $thn = isset($t['tahun_bayar']) ? $t['tahun_bayar'] : intval(date('Y', strtotime($t['tanggal'])));
                                echo $nama_bulan[$bln].' '.$thn;
                                ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    if ($t['status']=='approved') echo 'bg-green-500/30 text-green-300';
                                    elseif ($t['status']=='pending') echo 'bg-yellow-500/30 text-yellow-300';
                                    else echo 'bg-red-500/30 text-red-300';
                                    ?>">
                                    <?php echo ucfirst($t['status']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs opacity-70 max-w-32 truncate">
                                <?php echo htmlspecialchars($t['keterangan'] ?? '-'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex items-center justify-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&status=<?php echo $filter_status; ?>&bulan=<?php echo $filter_bulan; ?>&search=<?php echo urlencode($search); ?>" 
               class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-all">← Prev</a>
            <?php endif; ?>
            
            <?php for ($p = max(1,$page-2); $p <= min($total_pages,$page+2); $p++): ?>
            <a href="?page=<?php echo $p; ?>&status=<?php echo $filter_status; ?>&bulan=<?php echo $filter_bulan; ?>&search=<?php echo urlencode($search); ?>"
               class="px-3 py-2 rounded-lg text-sm transition-all <?php echo $p==$page ? 'bg-yellow-400 text-green-900 font-bold' : 'bg-white/10 hover:bg-white/20'; ?>">
                <?php echo $p; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&status=<?php echo $filter_status; ?>&bulan=<?php echo $filter_bulan; ?>&search=<?php echo urlencode($search); ?>"
               class="px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-all">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.
</footer>
</body>
</html>