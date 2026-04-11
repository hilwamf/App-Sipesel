<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}

require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));

// Ambil statistik untuk dashboard
// Total Pendapatan (dari transaksi yang approved)
$query_pendapatan = "SELECT COALESCE(SUM(nominal), 0) as total_pendapatan 
                     FROM transaksi WHERE status = 'approved'";
$result_pendapatan = mysqli_query($conn, $query_pendapatan);
$total_pendapatan = mysqli_fetch_assoc($result_pendapatan)['total_pendapatan'];

// Total Tunggakan (simulasi - bisa disesuaikan dengan logic bisnis)
$total_tunggakan = 5250000; // Bisa dihitung dari pedagang yang belum bayar

// Jumlah Pedagang Aktif
$query_pedagang = "SELECT COUNT(*) as total_pedagang FROM users WHERE role = 'pedagang'";
$result_pedagang = mysqli_query($conn, $query_pedagang);
$total_pedagang = mysqli_fetch_assoc($result_pedagang)['total_pedagang'];

// Jumlah Pengawas
$query_pengawas = "SELECT COUNT(*) as total_pengawas FROM users WHERE role = 'pengawas'";
$result_pengawas = mysqli_query($conn, $query_pengawas);
$total_pengawas = mysqli_fetch_assoc($result_pengawas)['total_pengawas'];

// Pembayaran Pending
$query_pending = "SELECT COUNT(*) as total_pending FROM transaksi WHERE status = 'pending'";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total_pending'];

// Kios Terisi vs Kosong (jika tabel kios ada)
$query_kios_terisi = "SELECT COUNT(*) as total_terisi FROM kios WHERE status = 'terisi'";
$result_kios_terisi = mysqli_query($conn, $query_kios_terisi);
if ($result_kios_terisi) {
    $total_kios_terisi = mysqli_fetch_assoc($result_kios_terisi)['total_terisi'];
} else {
    $total_kios_terisi = 0;
}

$query_kios_total = "SELECT COUNT(*) as total_kios FROM kios";
$result_kios_total = mysqli_query($conn, $query_kios_total);
if ($result_kios_total) {
    $total_kios = mysqli_fetch_assoc($result_kios_total)['total_kios'];
} else {
    $total_kios = 0;
}
$total_kios_kosong = $total_kios - $total_kios_terisi;

// Transaksi Terbaru (5 terakhir)
$query_transaksi = "SELECT t.*, u.nama, u.username 
                    FROM transaksi t 
                    JOIN users u ON t.id_user = u.id_user 
                    ORDER BY t.created_at DESC LIMIT 5";
$result_transaksi = mysqli_query($conn, $query_transaksi);
$transaksi_terbaru = [];
if($result_transaksi){
    while ($row = mysqli_fetch_assoc($result_transaksi)) {
        $transaksi_terbaru[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
    </style>
</head>

<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
    <div class="absolute inset-0 bg-black/40"></div>

    <header class="bg-green-800 shadow-xl sticky top-0 z-50">
        <div class="border-b border-green-600/50 py-2 px-6">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family: 'Montserrat', sans-serif;">
                    SIPESEL
                </span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">
                    Sistem Informasi Pembayaran Pajak Pasar - ADMIN PANEL
                </span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">
                        <?php echo $inisial; ?>
                    </div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block">
                        <?php echo htmlspecialchars($username); ?>
                    </span>
                    <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">
                        ADMIN
                    </span>
                </div>
            </div>
        </div>
        <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
            <a href="dashboard admin.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm flex items-center gap-2 whitespace-nowrap">
                <span>Dashboard</span>
            </a>
            <a href="verifikasi_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Verifikasi</span>
                <?php if ($total_pending > 0): ?>
                <span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?php echo $total_pending; ?></span>
                <?php endif; ?>
            </a>
            <a href="manajemen_user.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Users</span>
            </a>
            <a href="manajemen_kios.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Kios</span>
            </a>
            <a href="laporan_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Laporan</span>
            </a>
            <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Monitoring</span>
            </a>
            <a href="setting_sistem.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Setting</span>
            </a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                    <span>Keluar</span>
                </a>
            </div>
        </nav>
    </header>

    <section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Dashboard Administrator</h1>
                <p class="text-sm md:text-base opacity-90">
                    Halo, <span class="text-yellow-400 font-semibold"><?php echo htmlspecialchars($username); ?></span> 👋
                    <br>Kelola seluruh sistem pembayaran pajak kios dengan mudah
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="stat-card bg-gradient-to-br from-green-600/40 to-green-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-500/30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs bg-green-500/30 px-2 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-1">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                    <p class="text-sm opacity-80">Total Pendapatan</p>
                </div>

                <div class="stat-card bg-gradient-to-br from-red-600/40 to-red-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-500/30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <span class="text-xs bg-red-500/30 px-2 py-1 rounded-full">Alert</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-1">Rp <?php echo number_format($total_tunggakan, 0, ',', '.'); ?></h3>
                    <p class="text-sm opacity-80">Total Tunggakan</p>
                </div>

                <div class="stat-card bg-gradient-to-br from-blue-600/40 to-blue-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-500/30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs bg-blue-500/30 px-2 py-1 rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-1"><?php echo $total_pedagang; ?></h3>
                    <p class="text-sm opacity-80">Total Pedagang</p>
                </div>

                <div class="stat-card bg-gradient-to-br from-purple-600/40 to-purple-800/40 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-500/30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="text-xs bg-purple-500/30 px-2 py-1 rounded-full">Status</span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-1"><?php echo $total_kios_terisi; ?> / <?php echo $total_kios; ?></h3>
                    <p class="text-sm opacity-80">Kios Terisi / Total</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <div class="lg:col-span-1 bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Actions
                    </h2>
                    <div class="space-y-3">
                        <a href="verifikasi_admin.php" class="block bg-yellow-500/20 hover:bg-yellow-500/30 border border-yellow-500/50 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Verifikasi Pembayaran</h3>
                                    <p class="text-xs opacity-80"><?php echo $total_pending; ?> pending</p>
                                </div>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="manajemen_user.php" class="block bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/50 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Kelola Pengguna</h3>
                                    <p class="text-xs opacity-80"><?php echo $total_pedagang + $total_pengawas; ?> users</p>
                                </div>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="laporan_admin.php" class="block bg-green-500/20 hover:bg-green-500/30 border border-green-500/50 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Export Laporan</h3>
                                    <p class="text-xs opacity-80">Download reports</p>
                                </div>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <a href="manajemen_kios.php" class="block bg-purple-500/20 hover:bg-purple-500/30 border border-purple-500/50 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold">Kelola Kios</h3>
                                    <p class="text-xs opacity-80"><?php echo $total_kios; ?> kios</p>
                                </div>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Transaksi Terbaru
                    </h2>
                    
                    <?php if (count($transaksi_terbaru) > 0): ?>
                    <div class="space-y-3">
                        <?php foreach ($transaksi_terbaru as $trx): ?>
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($trx['nama']); ?></h3>
                                    <p class="text-xs opacity-80">Kios: <?php echo htmlspecialchars($trx['nomor_kios']); ?> | <?php echo htmlspecialchars($trx['metode_pembayaran']); ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    <?php 
                                        if ($trx['status'] == 'approved') echo 'bg-green-500/30 text-green-300';
                                        elseif ($trx['status'] == 'pending') echo 'bg-yellow-500/30 text-yellow-300';
                                        else echo 'bg-red-500/30 text-red-300';
                                    ?>">
                                    <?php echo ucfirst($trx['status']); ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span>Rp <?php echo number_format($trx['nominal'], 0, ',', '.'); ?></span>
                                <span class="text-xs opacity-70"><?php echo date('d M Y', strtotime($trx['tanggal'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 opacity-60">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Belum ada transaksi</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center">
                        <a href="monitoring_admin.php" class="text-yellow-400 hover:text-yellow-300 text-sm font-medium">
                            Lihat Semua Transaksi →
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold">Informasi Sistem</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white/5 rounded-lg p-4">
                        <p class="opacity-70 mb-1">Total Pengguna</p>
                        <p class="text-lg font-semibold"><?php echo $total_pedagang + $total_pengawas + 1; ?> users</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4">
                        <p class="opacity-70 mb-1">Pembayaran Bulan Ini</p>
                        <p class="text-lg font-semibold"><?php echo count($transaksi_terbaru); ?> transaksi</p>
                    </div>
                    <div class="bg-white/5 rounded-lg p-4">
                        <p class="opacity-70 mb-1">Tingkat Kepatuhan</p>
                        <p class="text-lg font-semibold"><?php echo $total_pedagang > 0 ? round(($total_pedagang - 4) / $total_pedagang * 100) : 0; ?>%</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">
        &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel. All rights reserved.
    </footer>

</body>
</html>