<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['role'])) {
    header("location:login.php?pesan=login_dulu");
    exit();
}

$username = $_SESSION['username'];
$nama     = $_SESSION['nama'] ?? $username;
$id_user  = $_SESSION['id_user'];
$inisial  = strtoupper(substr($username, 0, 1));

// ── Ambil notifikasi yang belum dibaca ────────────────────────────────────
$sql_notif = "SELECT * FROM notifikasi WHERE id_user = $id_user ORDER BY created_at DESC LIMIT 10";
$res_notif = mysqli_query($conn, $sql_notif);
$notifikasi = [];
$unread     = 0;
if ($res_notif) {
    while ($n = mysqli_fetch_assoc($res_notif)) {
        $notifikasi[] = $n;
        if (!$n['dibaca']) $unread++;
    }
}

// ── Tandai semua notif dibaca ketika halaman dibuka ───────────────────────
if ($unread > 0) {
    mysqli_query($conn, "UPDATE notifikasi SET dibaca = 1 WHERE id_user = $id_user AND dibaca = 0");
}

// ── Ambil info kios & transaksi terakhir pedagang ─────────────────────────
$no_kios = $_SESSION['no_kios'] ?? '-';
$sql_last = "SELECT jenis_pajak, nominal, tanggal, status FROM transaksi
             WHERE id_user = $id_user AND status = 'approved'
             ORDER BY tanggal DESC LIMIT 1";
$res_last   = mysqli_query($conn, $sql_last);
$last_trx   = $res_last ? mysqli_fetch_assoc($res_last) : null;

// Hitung jatuh tempo berikutnya
$jatuh_tempo_str = null;
$sisa_hari       = null;
$sudah_lewat     = false;
if ($last_trx) {
    $interval_map = ['Harian' => 1, 'Mingguan' => 7, 'Bulanan' => 30];
    $hari         = $interval_map[$last_trx['jenis_pajak']] ?? 30;
    $jt           = new DateTime($last_trx['tanggal']);
    $jt->modify("+{$hari} days");
    $now          = new DateTime();
    $diff         = (int)$now->diff($jt)->days;
    $sudah_lewat  = ($jt < $now);
    $sisa_hari    = $diff;
    $jatuh_tempo_str = $jt->format('d M Y');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        #notifPanel { display: none; }
        #notifPanel.show { display: block; }
    </style>
</head>

<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Header -->
    <header class="bg-green-800 shadow-xl sticky top-0 z-50">
        <div class="border-b border-green-600/50 py-2 px-6">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
                <div class="flex items-center gap-3">

                    <!-- Bell Notifikasi -->
                    <div class="relative">
                        <button onclick="toggleNotif()" class="relative p-2 rounded-lg hover:bg-white/10 transition-all" title="Notifikasi">
                            <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <?php if ($unread > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                <?= $unread > 9 ? '9+' : $unread ?>
                            </span>
                            <?php endif; ?>
                        </button>

                        <!-- Panel Notifikasi -->
                        <div id="notifPanel" class="absolute right-0 top-12 w-80 bg-green-900/95 backdrop-blur-md border border-white/20 rounded-xl shadow-2xl overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                                <p class="font-semibold text-sm">Notifikasi</p>
                                <button onclick="toggleNotif()" class="text-white/50 hover:text-white text-xs">Tutup</button>
                            </div>
                            <?php if (empty($notifikasi)): ?>
                            <div class="px-4 py-8 text-center text-white/40 text-sm">Tidak ada notifikasi</div>
                            <?php else: ?>
                            <div class="max-h-72 overflow-y-auto">
                                <?php foreach ($notifikasi as $n): ?>
                                <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-all <?= !$n['dibaca'] ? 'bg-yellow-500/10' : '' ?>">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-full bg-yellow-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-white/90 leading-relaxed"><?= htmlspecialchars($n['pesan']) ?></p>
                                            <p class="text-xs text-white/40 mt-1"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></p>
                                        </div>
                                        <?php if (!$n['dibaca']): ?>
                                        <div class="w-2 h-2 bg-yellow-400 rounded-full mt-1.5 flex-shrink-0"></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?= $inisial ?></div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block"><?= htmlspecialchars($username) ?></span>
                </div>
            </div>
        </div>
        <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
            <a href="dashboard.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm"><span>Dashboard</span></a>
            <a href="pembayaran.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Pembayaran</span></a>
            <a href="riwayat.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Riwayat Bayar</span></a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200"><span>Keluar</span></a>
            </div>
        </nav>
    </header>

    <!-- Content -->
    <section class="relative z-10 px-6 md:px-20 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto">

            <!-- Greeting -->
            <div class="mb-10">
                <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-2">Selamat Datang,</h1>
                <h2 class="text-2xl text-yellow-400 font-semibold"><?= htmlspecialchars($nama) ?> 👋</h2>
                <p class="text-sm opacity-70 mt-2">Kios <span class="font-semibold text-white"><?= htmlspecialchars($no_kios) ?></span></p>
            </div>

            <!-- Alert jatuh tempo -->
            <?php if ($sudah_lewat): ?>
            <div class="bg-red-500/20 border border-red-400/40 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-300 text-sm">Tagihan Anda Sudah Jatuh Tempo!</p>
                    <p class="text-xs text-red-200/80 mt-0.5">Jatuh tempo: <?= $jatuh_tempo_str ?> (terlambat <?= $sisa_hari ?> hari). Segera lakukan pembayaran.</p>
                    <a href="pembayaran.php" class="inline-block mt-2 px-4 py-1.5 bg-red-500/40 hover:bg-red-500/60 border border-red-400/50 rounded-lg text-xs font-semibold transition-all">Bayar Sekarang</a>
                </div>
            </div>
            <?php elseif ($jatuh_tempo_str && $sisa_hari <= 3): ?>
            <div class="bg-yellow-500/20 border border-yellow-400/40 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-yellow-300 text-sm">Tagihan Anda Segera Jatuh Tempo</p>
                    <p class="text-xs text-yellow-200/80 mt-0.5">Sisa <?= $sisa_hari ?> hari lagi (<?= $jatuh_tempo_str ?>). Jangan sampai terlambat!</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

                <!-- Tagihan berikutnya -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                    <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Tagihan Berikutnya</p>
                    <?php if ($jatuh_tempo_str): ?>
                        <p class="text-lg font-bold"><?= $jatuh_tempo_str ?></p>
                        <p class="text-xs mt-1 <?= $sudah_lewat ? 'text-red-300' : 'text-green-300' ?>">
                            <?= $sudah_lewat ? 'Terlambat '.$sisa_hari.' hari' : 'Sisa '.$sisa_hari.' hari' ?>
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-white/50">Belum ada transaksi</p>
                    <?php endif; ?>
                </div>

                <!-- Jenis Pajak terakhir -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                    <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Jenis Pajak Terakhir</p>
                    <?php if ($last_trx): ?>
                        <p class="text-lg font-bold"><?= htmlspecialchars($last_trx['jenis_pajak']) ?></p>
                        <p class="text-xs mt-1 text-white/50">Rp <?= number_format($last_trx['nominal'], 0, ',', '.') ?></p>
                    <?php else: ?>
                        <p class="text-sm text-white/50">-</p>
                    <?php endif; ?>
                </div>

                <!-- Nomor Kios -->
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                    <p class="text-xs text-green-300 mb-2 font-medium uppercase tracking-wider">Nomor Kios</p>
                    <p class="text-2xl font-bold tracking-widest" style="font-family:'Montserrat',sans-serif;"><?= htmlspecialchars($no_kios) ?></p>
                </div>
            </div>

            <!-- Quick links -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="pembayaran.php" class="bg-green-700/60 hover:bg-green-700/80 border border-green-500/40 rounded-xl p-5 flex items-center gap-4 transition-all hover:-translate-y-0.5">
                    <div class="w-12 h-12 bg-green-500/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Bayar Pajak</p>
                        <p class="text-xs text-white/60">Lakukan pembayaran sekarang</p>
                    </div>
                </a>
                <a href="riwayat.php" class="bg-white/10 hover:bg-white/15 border border-white/20 rounded-xl p-5 flex items-center gap-4 transition-all hover:-translate-y-0.5">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Riwayat Bayar</p>
                        <p class="text-xs text-white/60">Lihat semua transaksi Anda</p>
                    </div>
                </a>
            </div>

        </div>
    </section>

    <script>
        function toggleNotif() {
            document.getElementById('notifPanel').classList.toggle('show');
        }
        // Tutup panel jika klik di luar
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('notifPanel');
            if (!panel.contains(e.target) && !e.target.closest('[onclick="toggleNotif()"]')) {
                panel.classList.remove('show');
            }
        });
    </script>
</body>
</html>