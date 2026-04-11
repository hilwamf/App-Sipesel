<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php?pesan=akses_ditolak");
    exit();
}

require_once 'koneksi.php';

$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1));
$id_admin = $_SESSION['id_user'];

// Handle verifikasi action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $sql = "UPDATE transaksi SET 
                status = 'approved', 
                verified_by = $id_admin, 
                verified_at = NOW(),
                catatan_verifikasi = 'Pembayaran disetujui oleh admin'
                WHERE id_transaksi = '$id_transaksi'";
        
        if (mysqli_query($conn, $sql)) {
            $success_msg = "Pembayaran berhasil disetujui!";
        } else {
            $error_msg = "Gagal menyetujui pembayaran: " . mysqli_error($conn);
        }
    } elseif ($action == 'reject') {
        $catatan = mysqli_real_escape_string($conn, $_POST['catatan_penolakan']);
        
        $sql = "UPDATE transaksi SET 
                status = 'rejected', 
                verified_by = $id_admin, 
                verified_at = NOW(),
                catatan_verifikasi = '$catatan'
                WHERE id_transaksi = '$id_transaksi'";
        
        if (mysqli_query($conn, $sql)) {
            $success_msg = "Pembayaran berhasil ditolak!";
        } else {
            $error_msg = "Gagal menolak pembayaran: " . mysqli_error($conn);
        }
    }
}

// Ambil data pembayaran pending
$query_pending = "SELECT t.*, u.nama, u.username, u.nomor_hp 
                  FROM transaksi t 
                  JOIN users u ON t.id_user = u.id_user 
                  WHERE t.status = 'pending'
                  ORDER BY t.created_at DESC";
$result_pending = mysqli_query($conn, $query_pending);

// Ambil data pembayaran yang sudah diverifikasi (approved & rejected)
$query_verified = "SELECT t.*, u.nama, u.username, v.username as verifier_name
                   FROM transaksi t 
                   JOIN users u ON t.id_user = u.id_user 
                   LEFT JOIN users v ON t.verified_by = v.id_user
                   WHERE t.status IN ('approved', 'rejected')
                   ORDER BY t.verified_at DESC
                   LIMIT 20";
$result_verified = mysqli_query($conn, $query_verified);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - SIPESEL Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .tab-active { 
            background: rgba(250, 204, 21, 0.2);
            border-color: rgba(250, 204, 21, 0.5);
            color: rgb(253, 224, 71);
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Modal Styles */
        .modal { display: none; }
        .modal.show { display: flex; }
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
                    <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-bold rounded ml-2">ADMIN</span>
                </div>
            </div>
        </div>
        <nav class="max-w-7xl mx-auto px-6 flex items-center gap-1 py-2 overflow-x-auto">
            <a href="Dashboard admin.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <span>Dashboard</span>
            </a>
            <a href="verifikasi_admin.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm flex items-center gap-2 whitespace-nowrap">
                <span>Verifikasi</span>
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
            
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Verifikasi Pembayaran</h1>
                <p class="text-sm md:text-base opacity-90">Approve atau reject pembayaran dari pedagang</p>
            </div>

            <?php if (isset($success_msg)): ?>
            <div class="bg-green-500/30 border border-green-500/50 text-white rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span><?php echo $success_msg; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
            <div class="bg-red-500/30 border border-red-500/50 text-white rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span><?php echo $error_msg; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 mb-6">
                <div class="flex flex-wrap gap-2 p-4 border-b border-white/20">
                    <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn tab-active px-4 py-2 rounded-lg text-sm font-medium transition-all border border-transparent flex items-center">
                        Menunggu Verifikasi
                        <?php if (mysqli_num_rows($result_pending) > 0): ?>
                        <span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs rounded-full"><?php echo mysqli_num_rows($result_pending); ?></span>
                        <?php endif; ?>
                    </button>
                    <button onclick="switchTab('verified')" id="tab-verified" class="tab-btn px-4 py-2 rounded-lg text-green-200 hover:bg-white/10 text-sm font-medium transition-all border border-transparent">
                        Riwayat Verifikasi
                    </button>
                </div>

                <div id="content-pending" class="tab-content active p-6">
                    <?php if (mysqli_num_rows($result_pending) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
                        <div class="bg-white/5 backdrop-blur-sm border border-white/20 rounded-xl p-6 hover:border-yellow-400/50 transition-all flex flex-col">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($row['nama']); ?></h3>
                                    <p class="text-sm opacity-80">@<?php echo htmlspecialchars($row['username']); ?></p>
                                </div>
                                <span class="px-3 py-1 bg-yellow-500/30 text-yellow-300 rounded-full text-xs font-medium whitespace-nowrap">
                                    Pending
                                </span>
                            </div>

                            <div class="space-y-2 mb-4 text-sm flex-grow">
                                <div class="flex justify-between">
                                    <span class="opacity-70">Kios:</span>
                                    <span class="font-semibold"><?php echo htmlspecialchars($row['nomor_kios']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="opacity-70">Nominal:</span>
                                    <span class="font-semibold text-green-400">Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="opacity-70">Jenis Pajak</span>
                                    <span class="font-semibold"><?php echo ucfirst($row['jenis_pajak']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="opacity-70">Tanggal:</span>
                                    <span class="font-semibold"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <?php if (!empty($row['bukti_bayar'])): ?>
                                <button onclick="showBukti('<?php echo htmlspecialchars($row['bukti_bayar']); ?>')" class="w-full mb-3 px-4 py-2 bg-blue-500/30 hover:bg-blue-500/50 border border-blue-500/50 rounded-lg text-sm font-medium transition-all">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Lihat Bukti
                                    </div>
                                </button>
                                <?php endif; ?>

                                <div class="grid grid-cols-2 gap-3">
                                    <form method="POST" onsubmit="return confirm('Setujui pembayaran ini?');" class="w-full">
                                        <input type="hidden" name="id_transaksi" value="<?php echo $row['id_transaksi']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="w-full px-4 py-2 bg-green-500/30 hover:bg-green-500/50 border border-green-500/50 rounded-lg text-sm font-medium transition-all">
                                            <div class="flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Approve
                                            </div>
                                        </button>
                                    </form>

                                    <button onclick="showRejectModal(<?php echo $row['id_transaksi']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')" class="px-4 py-2 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded-lg text-sm font-medium transition-all w-full">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-16 opacity-60">
                        <svg class="w-20 h-20 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold mb-2">Tidak Ada Pembayaran Pending</h3>
                        <p class="text-sm">Semua pembayaran sudah diverifikasi</p>
                    </div>
                    <?php endif; ?>
                </div>

                <div id="content-verified" class="tab-content p-6">
                    <?php if (mysqli_num_rows($result_verified) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white/10 border-b border-white/20">
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Pedagang</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Kios</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Periode</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Nominal</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Status</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Verifikator</th>
                                    <th class="px-6 py-4 text-sm font-semibold whitespace-nowrap">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_verified)): ?>
                                <tr class="border-b border-white/10 hover:bg-white/5 transition-all">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="font-semibold"><?php echo htmlspecialchars($row['nama']); ?></div>
                                            <div class="text-xs opacity-70">@<?php echo htmlspecialchars($row['username']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold whitespace-nowrap"><?php echo htmlspecialchars($row['nomor_kios']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                    <td class="px-6 py-4 font-semibold text-green-400 whitespace-nowrap">Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php echo $row['status'] == 'approved' ? 'bg-green-500/30 text-green-300' : 'bg-red-500/30 text-red-300'; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap"><?php echo htmlspecialchars($row['verifier_name'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap"><?php echo date('d M Y', strtotime($row['verified_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-16 opacity-60">
                        <p>Belum ada riwayat verifikasi</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div id="modalBukti" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 w-full max-w-2xl border border-white/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Bukti Pembayaran</h3>
                <button onclick="closeBukti()" class="text-white hover:text-red-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="buktImage" src="" alt="Bukti Pembayaran" class="w-full max-h-[70vh] object-contain rounded-lg">
        </div>
    </div>

    <div id="modalReject" class="modal fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 w-full max-w-md border border-white/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Tolak Pembayaran</h3>
                <button onclick="closeRejectModal()" class="text-white hover:text-red-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm opacity-90 mb-4">Berikan alasan penolakan untuk <span id="rejectNama" class="font-semibold text-yellow-400"></span>:</p>
            
            <form method="POST">
                <input type="hidden" name="id_transaksi" id="rejectId">
                <input type="hidden" name="action" value="reject">
                
                <textarea name="catatan_penolakan" rows="4" required placeholder="Contoh: Bukti pembayaran tidak jelas, nominal tidak sesuai, dll."
                    class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-red-400 mb-4"></textarea>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg font-medium transition-all">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-500/30 hover:bg-red-500/50 border border-red-500/50 rounded-lg font-medium transition-all">
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mencegah form tersubmit ulang saat halaman di-refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('text-green-200', 'hover:bg-white/10');
            });
            
            document.getElementById('content-' + tab).classList.add('active');
            
            const activeBtn = document.getElementById('tab-' + tab);
            activeBtn.classList.add('tab-active');
            activeBtn.classList.remove('text-green-200', 'hover:bg-white/10');
        }

        function showBukti(imagePath) {
            document.getElementById('buktImage').src = imagePath;
            document.getElementById('modalBukti').classList.add('show');
        }

        function closeBukti() {
            document.getElementById('modalBukti').classList.remove('show');
            document.getElementById('buktImage').src = ""; // Clear image
        }

        function showRejectModal(id, nama) {
            document.getElementById('rejectId').value = id;
            document.getElementById('rejectNama').textContent = nama;
            document.getElementById('modalReject').classList.add('show');
        }

        function closeRejectModal() {
            document.getElementById('modalReject').classList.remove('show');
        }

        // Close modal jika di-klik di luar box modal
        document.getElementById('modalBukti').addEventListener('click', function(e) {
            if (e.target === this) closeBukti();
        });
        
        document.getElementById('modalReject').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>

</body>
</html>