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

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        
        if ($_POST['action'] == 'tambah') {
            $no_kios = mysqli_real_escape_string($conn, $_POST['no_kios']);
            $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
            $ukuran = floatval($_POST['ukuran']);
            $tarif = floatval($_POST['tarif_bulanan']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
            
            $sql = "INSERT INTO kios (no_kios, lokasi, ukuran, tarif_bulanan, status, keterangan) 
                    VALUES ('$no_kios', '$lokasi', $ukuran, $tarif, '$status', '$keterangan')";
            if (mysqli_query($conn, $sql)) {
                $pesan = "Kios berhasil ditambahkan!";
            } else {
                $error = "Gagal: " . mysqli_error($conn);
            }
        }
        
        if ($_POST['action'] == 'edit') {
            $id = intval($_POST['id_kios']);
            $no_kios = mysqli_real_escape_string($conn, $_POST['no_kios']);
            $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
            $ukuran = floatval($_POST['ukuran']);
            $tarif = floatval($_POST['tarif_bulanan']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
            
            $sql = "UPDATE kios SET no_kios='$no_kios', lokasi='$lokasi', ukuran=$ukuran, 
                    tarif_bulanan=$tarif, status='$status', keterangan='$keterangan' 
                    WHERE id_kios=$id";
            if (mysqli_query($conn, $sql)) {
                $pesan = "Kios berhasil diperbarui!";
            } else {
                $error = "Gagal: " . mysqli_error($conn);
            }
        }
        
        if ($_POST['action'] == 'hapus') {
            $id = intval($_POST['id_kios']);
            $sql = "DELETE FROM kios WHERE id_kios=$id";
            if (mysqli_query($conn, $sql)) {
                $pesan = "Kios berhasil dihapus!";
            } else {
                $error = "Gagal hapus: " . mysqli_error($conn);
            }
        }
    }
}

// Filter & Search
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($filter_status) $where .= " AND status = '$filter_status'";
if ($search) $where .= " AND (no_kios LIKE '%$search%' OR lokasi LIKE '%$search%')";

$query = "SELECT k.*, 
          (SELECT u.nama FROM sewa_kios sk JOIN users u ON sk.id_user = u.id_user 
           WHERE sk.id_kios = k.id_kios AND sk.status_sewa = 'aktif' LIMIT 1) as nama_penyewa
          FROM kios k $where ORDER BY k.no_kios";
$result = mysqli_query($conn, $query);
$kios_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $kios_list[] = $row;
}

// Statistik
$stat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(*) as total,
    SUM(status='terisi') as terisi,
    SUM(status='kosong') as kosong,
    SUM(status='maintenance') as maintenance
    FROM kios"));

// Pending count untuk badge nav
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM transaksi WHERE status='pending'"))['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kios - SIPESEL</title>
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
            <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar - ADMIN PANEL</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?php echo $inisial; ?></div>
                <span class="text-green-100 text-sm font-medium hidden sm:block"><?php echo htmlspecialchars($username); ?></span>
                <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">ADMIN</span>
            </div>
        </div>
    </div>
    <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
        <a href="Dashboard_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Dashboard</a>
        <a href="verifikasi_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all flex items-center gap-2 whitespace-nowrap">
            Verifikasi
            <?php if ($total_pending > 0): ?><span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?php echo $total_pending; ?></span><?php endif; ?>
        </a>
        <a href="manajemen_user.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Users</a>
        <a href="manajemen_kios.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm whitespace-nowrap">Kios</a>
        <a href="laporan_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Laporan</a>
        <a href="monitoring_admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Monitoring</a>
        <a href="setting_sistem.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 text-sm font-medium transition-all whitespace-nowrap">Setting</a>
        <div class="ml-auto">
            <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all whitespace-nowrap">Keluar</a>
        </div>
    </nav>
</header>

<section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold">Manajemen Kios</h1>
                <p class="text-sm opacity-80">Kelola data kios pasar</p>
            </div>
            <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
                class="bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold px-6 py-2 rounded-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kios
            </button>
        </div>

        <!-- Alert -->
        <?php if ($pesan): ?>
        <div class="bg-green-500/30 border border-green-400 text-green-200 px-4 py-3 rounded-lg mb-4"><?php echo $pesan; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="bg-red-500/30 border border-red-400 text-red-200 px-4 py-3 rounded-lg mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Statistik Kios -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 text-center">
                <p class="text-3xl font-bold"><?php echo $stat['total']; ?></p>
                <p class="text-xs opacity-70 mt-1">Total Kios</p>
            </div>
            <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-4 border border-green-400/30 text-center">
                <p class="text-3xl font-bold text-green-300"><?php echo $stat['terisi']; ?></p>
                <p class="text-xs opacity-70 mt-1">Terisi</p>
            </div>
            <div class="bg-yellow-500/20 backdrop-blur-md rounded-xl p-4 border border-yellow-400/30 text-center">
                <p class="text-3xl font-bold text-yellow-300"><?php echo $stat['kosong']; ?></p>
                <p class="text-xs opacity-70 mt-1">Kosong</p>
            </div>
            <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-4 border border-red-400/30 text-center">
                <p class="text-3xl font-bold text-red-300"><?php echo $stat['maintenance']; ?></p>
                <p class="text-xs opacity-70 mt-1">Maintenance</p>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="Cari no kios / lokasi..." 
                    class="bg-white/10 border border-white/30 rounded-lg px-4 py-2 text-sm text-white placeholder-white/50 focus:outline-none focus:border-yellow-400">
                <select name="status" class="bg-green-900 border border-white/30 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-yellow-400">
                    <option value="">Semua Status</option>
                    <option value="terisi" <?php echo $filter_status=='terisi'?'selected':''; ?>>Terisi</option>
                    <option value="kosong" <?php echo $filter_status=='kosong'?'selected':''; ?>>Kosong</option>
                    <option value="maintenance" <?php echo $filter_status=='maintenance'?'selected':''; ?>>Maintenance</option>
                </select>
                <button type="submit" class="bg-yellow-400 text-green-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-yellow-300 transition-all">Filter</button>
                <a href="manajemen_kios.php" class="text-green-300 hover:text-white text-sm">Reset</a>
            </form>
        </div>

        <!-- Tabel Kios -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-green-800/60 text-left">
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">No. Kios</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Lokasi</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Ukuran (m²)</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Tarif/Bulan</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Penyewa</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php if (empty($kios_list)): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center opacity-50">Tidak ada data kios</td></tr>
                        <?php endif; ?>
                        <?php foreach ($kios_list as $k): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-yellow-300"><?php echo htmlspecialchars($k['no_kios']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($k['lokasi']); ?></td>
                            <td class="px-4 py-3"><?php echo number_format($k['ukuran'], 2); ?></td>
                            <td class="px-4 py-3">Rp <?php echo number_format($k['tarif_bulanan'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-3 text-sm opacity-80"><?php echo $k['nama_penyewa'] ? htmlspecialchars($k['nama_penyewa']) : '-'; ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    if ($k['status']=='terisi') echo 'bg-green-500/30 text-green-300';
                                    elseif ($k['status']=='kosong') echo 'bg-yellow-500/30 text-yellow-300';
                                    else echo 'bg-red-500/30 text-red-300';
                                    ?>">
                                    <?php echo ucfirst($k['status']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button onclick='editKios(<?php echo json_encode($k); ?>)' 
                                        class="bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 px-3 py-1 rounded-lg text-xs transition-all">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Hapus kios <?php echo $k['no_kios']; ?>?')">
                                        <input type="hidden" name="action" value="hapus">
                                        <input type="hidden" name="id_kios" value="<?php echo $k['id_kios']; ?>">
                                        <button type="submit" class="bg-red-500/30 hover:bg-red-500/50 text-red-300 px-3 py-1 rounded-lg text-xs transition-all">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- MODAL TAMBAH KIOS -->
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-green-900 border border-white/20 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Tambah Kios Baru</h2>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-white/60 hover:text-white">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="tambah">
            <div class="space-y-3">
                <div>
                    <label class="text-xs opacity-70 block mb-1">Nomor Kios</label>
                    <input type="text" name="no_kios" required placeholder="cth: A-01" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Lokasi</label>
                    <input type="text" name="lokasi" required placeholder="cth: Blok A Lantai 1" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Ukuran (m²)</label>
                        <input type="number" name="ukuran" step="0.01" value="10.00" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Tarif/Bulan (Rp)</label>
                        <input type="number" name="tarif_bulanan" value="250000" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Status</label>
                    <select name="status" class="w-full bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                        <option value="kosong">Kosong</option>
                        <option value="terisi">Terisi</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Keterangan (opsional)</label>
                    <textarea name="keterangan" rows="2" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold py-2 rounded-lg transition-all">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="flex-1 bg-white/10 hover:bg-white/20 py-2 rounded-lg transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KIOS -->
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-green-900 border border-white/20 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Edit Kios</h2>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-white/60 hover:text-white">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_kios" id="edit_id_kios">
            <div class="space-y-3">
                <div>
                    <label class="text-xs opacity-70 block mb-1">Nomor Kios</label>
                    <input type="text" name="no_kios" id="edit_no_kios" required class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi" required class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Ukuran (m²)</label>
                        <input type="number" name="ukuran" id="edit_ukuran" step="0.01" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                    <div>
                        <label class="text-xs opacity-70 block mb-1">Tarif/Bulan (Rp)</label>
                        <input type="number" name="tarif_bulanan" id="edit_tarif" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                    </div>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full bg-green-900 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400">
                        <option value="kosong">Kosong</option>
                        <option value="terisi">Terisi</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs opacity-70 block mb-1">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full bg-white/10 border border-white/30 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-yellow-400"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-green-900 font-bold py-2 rounded-lg transition-all">Update</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="flex-1 bg-white/10 hover:bg-white/20 py-2 rounded-lg transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

<footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10 relative z-10">
    &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Admin Panel.
</footer>

<script>
function editKios(data) {
    document.getElementById('edit_id_kios').value = data.id_kios;
    document.getElementById('edit_no_kios').value = data.no_kios;
    document.getElementById('edit_lokasi').value = data.lokasi;
    document.getElementById('edit_ukuran').value = data.ukuran;
    document.getElementById('edit_tarif').value = data.tarif_bulanan;
    document.getElementById('edit_status').value = data.status;
    document.getElementById('edit_keterangan').value = data.keterangan || '';
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
</body>
</html>