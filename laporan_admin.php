<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}
require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));

// Filter periode
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('m'));
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Nama bulan
$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

// Query transaksi dengan filter
$where = "WHERE MONTH(t.tanggal) = $bulan AND YEAR(t.tanggal) = $tahun";
if ($filter_status) $where .= " AND t.status = '$filter_status'";

$query = "SELECT t.*, u.nama, u.username, u.no_kios as kios_user
          FROM transaksi t
          JOIN users u ON t.id_user = u.id_user
          $where
          ORDER BY t.tanggal DESC, t.created_at DESC";
$result = mysqli_query($conn, $query);
$transaksi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $transaksi[] = $row;
}

// Ringkasan keuangan bulan ini
$ringkasan = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT 
        COALESCE(SUM(CASE WHEN status='approved' THEN nominal ELSE 0 END), 0) as total_masuk,
        COALESCE(SUM(CASE WHEN status='pending' THEN nominal ELSE 0 END), 0) as total_pending,
        COALESCE(SUM(CASE WHEN status='rejected' THEN nominal ELSE 0 END), 0) as total_ditolak,
        COUNT(CASE WHEN status='approved' THEN 1 END) as jml_approved,
        COUNT(CASE WHEN status='pending' THEN 1 END) as jml_pending,
        COUNT(*) as total_transaksi
     FROM transaksi WHERE MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun"
));

// Rekap per pedagang bulan ini
$rekap_pedagang = [];
$q_rekap = mysqli_query($conn, 
    "SELECT u.nama, u.username, u.no_kios,
        COALESCE(SUM(CASE WHEN t.status='approved' THEN t.nominal ELSE 0 END), 0) as bayar,
        COUNT(CASE WHEN t.status='approved' THEN 1 END) as jml_bayar,
        MAX(t.tanggal) as last_bayar
     FROM users u
     LEFT JOIN transaksi t ON u.id_user = t.id_user AND MONTH(t.tanggal)=$bulan AND YEAR(t.tanggal)=$tahun
     WHERE u.role = 'pedagang'
     GROUP BY u.id_user
     ORDER BY u.nama");
if ($q_rekap) {
    while ($r = mysqli_fetch_assoc($q_rekap)) $rekap_pedagang[] = $r;
}

// Hitung pedagang belum bayar bulan ini
$total_pedagang = count($rekap_pedagang);
$sudah_bayar = count(array_filter($rekap_pedagang, fn($p) => $p['jml_bayar'] > 0));
$belum_bayar = $total_pedagang - $sudah_bayar;
$tingkat_kepatuhan = $total_pedagang > 0 ? round($sudah_bayar / $total_pedagang * 100) : 0;

// Pending nav badge
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM transaksi WHERE status='pending'"))['c'];

// Handle export CSV
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_'.$bulan.'_'.$tahun.'.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['No', 'Nama', 'Username', 'No Kios', 'Tanggal', 'Nominal', 'Metode', 'Status', 'Keterangan']);
    $no = 1;
    foreach ($transaksi as $t) {
        fputcsv($output, [
            $no++, $t['nama'], $t['username'], $t['nomor_kios'] ?? '-',
            $t['tanggal'], $t['nominal'], $t['metode_pembayaran'], $t['status'], $t['keterangan'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-section { background: white !important; color: black !important; }
        }
    </style>
</head>
<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
<div class="absolute inset-0 bg-black/40"></div>

<!-- HEADER -->
<header class="bg-green-800 shadow-xl sticky top-0 z-50 no-print">
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
        <a href="laporan_admin.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm whitespace-nowrap">Laporan</a>
        <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Monitoring</a>
        <a href="setting_sistem.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium whitespace-nowrap">Setting</a>
        <div class="ml-auto">
            <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold whitespace-nowrap">Keluar</a>
        </div>
    </nav>
</header>

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">

        <div class="mb-6 flex items-center justify-between flex-wrap gap-4 no-print">
            <div>
                <h1 class="text-3xl font-bold">Laporan Pembayaran</h1>
                <p class="text-sm opacity-80"><?php echo $nama_bulan[$bulan].' '.$tahun; ?></p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&export=csv<?php echo $filter_status ? '&status='.$filter_status : ''; ?>" 
                   class="bg-green-500/80 hover:bg-green-500 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    Export CSV
                </a>
                <button onclick="window.print()" class="bg-blue-500/80 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6 no-print">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs opacity-70 block mb-1">Bulan</label>
                    <select name="bulan" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                        <?php for ($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i==$bulan?'selected':''; ?>><?php echo $nama_bulan[$i]; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Tahun</label>
                    <select name="tahun" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                        <?php for ($y=2024; $y<=2027; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y==$tahun?'selected':''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Status</label>
                    <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                        <option value="">Semua</option>
                        <option value="approved" <?php echo $filter_status=='approved'?'selected':''; ?>>Approved</option>
                        <option value="pending" <?php echo $filter_status=='pending'?'selected':''; ?>>Pending</option>
                        <option value="rejected" <?php echo $filter_status=='rejected'?'selected':''; ?>>Rejected</option>
                    </select>
                </div>
                <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Tampilkan</button>
            </form>
        </div>

        <!-- Ringkasan Keuangan -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30">
                <p class="text-xs opacity-70 mb-1">Total Masuk</p>
                <p class="text-xl font-bold text-green-300">Rp <?php echo number_format($ringkasan['total_masuk'], 0, ',', '.'); ?></p>
                <p class="text-xs opacity-60 mt-1"><?php echo $ringkasan['jml_approved']; ?> transaksi</p>
            </div>
            <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30">
                <p class="text-xs opacity-70 mb-1">Pending</p>
                <p class="text-xl font-bold text-yellow-300">Rp <?php echo number_format($ringkasan['total_pending'], 0, ',', '.'); ?></p>
                <p class="text-xs opacity-60 mt-1"><?php echo $ringkasan['jml_pending']; ?> transaksi</p>
            </div>
            <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30">
                <p class="text-xs opacity-70 mb-1">Kios Belum Bayar</p>
                <p class="text-xl font-bold text-red-300"><?php echo $belum_bayar; ?> kios</p>
                <p class="text-xs opacity-60 mt-1">dari <?php echo $total_pedagang; ?> pedagang</p>
            </div>
            <div class="bg-blue-500/20 backdrop-blur-md rounded-xl p-4 border border-blue-400/30">
                <p class="text-xs opacity-70 mb-1">Tingkat Kepatuhan</p>
                <p class="text-xl font-bold text-blue-300"><?php echo $tingkat_kepatuhan; ?>%</p>
                <p class="text-xs opacity-60 mt-1"><?php echo $sudah_bayar; ?>/<?php echo $total_pedagang; ?> pedagang</p>
            </div>
        </div>

        <!-- Tab -->
        <div class="mb-4 no-print">
            <div class="flex gap-2">
                <button onclick="showTab('transaksi')" id="tab-transaksi" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm">Detail Transaksi</button>
                <button onclick="showTab('rekap')" id="tab-rekap" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all">Rekap Per Pedagang</button>
            </div>
        </div>

        <!-- Tabel Detail Transaksi -->
        <div id="panel-transaksi" class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-6">
            <div class="px-4 py-3 bg-green-800/60 font-semibold text-sm">
                Detail Transaksi — <?php echo $nama_bulan[$bulan].' '.$tahun; ?>
                <span class="ml-2 text-xs opacity-70">(<?php echo count($transaksi); ?> data)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-xs opacity-70">
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">No Kios</th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-right">Nominal</th>
                            <th class="px-4 py-2 text-left">Metode</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php if (empty($transaksi)): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center opacity-50">Tidak ada transaksi pada periode ini</td></tr>
                        <?php endif; ?>
                        <?php foreach ($transaksi as $i => $t): ?>
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-2 opacity-60"><?php echo $i+1; ?></td>
                            <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($t['nama']); ?></td>
                            <td class="px-4 py-2 font-mono text-yellow-300"><?php echo htmlspecialchars($t['nomor_kios'] ?? '-'); ?></td>
                            <td class="px-4 py-2"><?php echo date('d M Y', strtotime($t['tanggal'])); ?></td>
                            <td class="px-4 py-2 text-right font-mono">Rp <?php echo number_format($t['nominal'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-2 text-xs opacity-80"><?php echo ucwords(str_replace('_', ' ', $t['metode_pembayaran'])); ?></td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    if ($t['status']=='approved') echo 'bg-green-500/30 text-green-300';
                                    elseif ($t['status']=='pending') echo 'bg-yellow-500/30 text-yellow-300';
                                    else echo 'bg-red-500/30 text-red-300';
                                    ?>">
                                    <?php echo ucfirst($t['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if (!empty($transaksi)): ?>
                    <tfoot>
                        <tr class="border-t border-white/20 bg-green-800/30 font-semibold">
                            <td colspan="4" class="px-4 py-3 text-right text-xs uppercase tracking-wider opacity-70">Total Approved:</td>
                            <td class="px-4 py-3 text-right text-green-300">Rp <?php echo number_format($ringkasan['total_masuk'], 0, ',', '.'); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Tabel Rekap Per Pedagang -->
        <div id="panel-rekap" class="hidden bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden mb-6">
            <div class="px-4 py-3 bg-green-800/60 font-semibold text-sm">
                Rekap Per Pedagang — <?php echo $nama_bulan[$bulan].' '.$tahun; ?>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-xs opacity-70">
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Nama Pedagang</th>
                            <th class="px-4 py-2 text-left">No Kios</th>
                            <th class="px-4 py-2 text-right">Total Bayar</th>
                            <th class="px-4 py-2 text-center">Tanggal Bayar</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php foreach ($rekap_pedagang as $i => $p): ?>
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-2 opacity-60"><?php echo $i+1; ?></td>
                            <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($p['nama']); ?></td>
                            <td class="px-4 py-2 font-mono text-yellow-300"><?php echo htmlspecialchars($p['no_kios'] ?? '-'); ?></td>
                            <td class="px-4 py-2 text-right font-mono">
                                <?php echo $p['bayar'] > 0 ? 'Rp '.number_format($p['bayar'], 0, ',', '.') : '-'; ?>
                            </td>
                            <td class="px-4 py-2 text-center text-xs">
                                <?php echo $p['last_bayar'] ? date('d M Y', strtotime($p['last_bayar'])) : '-'; ?>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?php if ($p['jml_bayar'] > 0): ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500/30 text-green-300">Sudah Bayar</span>
                                <?php else: ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-500/30 text-red-300">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10 no-print">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.
</footer>

<script>
function showTab(tab) {
    document.getElementById('panel-transaksi').classList.add('hidden');
    document.getElementById('panel-rekap').classList.add('hidden');
    document.getElementById('tab-transaksi').className = 'px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all';
    document.getElementById('tab-rekap').className = 'px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all';
    
    document.getElementById('panel-'+tab).classList.remove('hidden');
    document.getElementById('tab-'+tab).className = 'px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm';
}
</script>
</body>
</html>