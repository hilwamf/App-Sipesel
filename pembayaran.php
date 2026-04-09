<?php
session_start();
require_once 'koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. Ambil Session
$username = $_SESSION['username'];
$id_user = $_SESSION['id_user'];
$inisial = strtoupper(substr($username, 0, 1));

// =========================================================
// 3. TARUH DI SINI: Ambil data no_kios untuk ditampilkan di form
// =========================================================
$query_user = "SELECT no_kios FROM users WHERE id_user = '$id_user'";
$result_user = mysqli_query($conn, $query_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $user_data = mysqli_fetch_assoc($result_user);
    $kios_default = $user_data['no_kios'];
} else {
    $kios_default = "";
}
// =========================================================

$transaksi_sukses = false;
$data_sukses = [];

// 4. Proses jika tombol Bayar Sekarang ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_bayar'])) {
    
    $nomor_kios = mysqli_real_escape_string($conn, $_POST['nomor_kios']);
    $jenis_pajak = mysqli_real_escape_string($conn, $_POST['jenis_pajak']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $nominal = mysqli_real_escape_string($conn, $_POST['nominal']);
    
    // Buat Nomor Transaksi Unik & Tanggal
    $no_trx = 'TRX-' . date('Ymd') . '-' . rand(100000, 999999);
    $tanggal = date('Y-m-d H:i:s');
    $tanggal_tampil = date('d F Y');

    // Insert ke Database Transaksi
    $query = "INSERT INTO transaksi (id_user, no_trx, nomor_kios, jenis_pajak, metode_pembayaran, nominal, tanggal, status) 
              VALUES ('$id_user', '$no_trx', '$nomor_kios', '$jenis_pajak', '$metode', '$nominal', '$tanggal', 'Berhasil')";
              
    if (mysqli_query($conn, $query)) {
        $transaksi_sukses = true;
        // Simpan data untuk ditampilkan di struk
        $data_sukses = [
            'no_trx' => $no_trx,
            'kios' => $nomor_kios,
            'jenis' => $jenis_pajak,
            'metode' => $metode,
            'nominal' => $nominal,
            'tanggal' => $tanggal_tampil
        ];
    } else {
        echo "<script>alert('Terjadi kesalahan database: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pajak - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Poppins','sans-serif'], heading: ['Montserrat','sans-serif'] } } }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .jenis-card { transition: all 0.25s ease; cursor: pointer; }
        .jenis-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(22,163,74,0.15); }
        .jenis-card.selected { border-color:#16a34a !important; background:linear-gradient(135deg,#f0fdf4,#dcfce7); box-shadow:0 0 0 3px rgba(22,163,74,0.2); }
        .jenis-card.selected .jenis-check { opacity:1; transform:scale(1); }
        .jenis-check { opacity:0; transform:scale(0.5); transition: all 0.2s; }
        .metode-card { transition: all 0.2s ease; cursor: pointer; }
        .metode-card:hover { border-color:#16a34a; background:#f0fdf4; }
        .metode-card.selected { border-color:#16a34a !important; background:#f0fdf4; box-shadow:0 0 0 3px rgba(22,163,74,0.15); }
        @keyframes popIn  { 0%{transform:scale(0.5) rotate(-10deg);opacity:0} 70%{transform:scale(1.1) rotate(3deg)} 100%{transform:scale(1) rotate(0deg);opacity:1} }
        @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .anim-pop  { animation: popIn  0.6s ease forwards; }
        .anim-fade { animation: fadeUp 0.5s ease forwards; }
        .delay-1 { animation-delay:0.3s; opacity:0; }
        .delay-2 { animation-delay:0.5s; opacity:0; }
        .delay-3 { animation-delay:0.7s; opacity:0; }
        .overlay { display:none; }
        .overlay.show { display:flex; }
        .page { display:none; }
        .page.active { display:block; }
    </style>
</head>
<body class="bg-green-50 text-neutral-900 min-h-screen">

    <header class="shadow-xl sticky top-0 z-50" style="background:linear-gradient(to right,#166534,#15803d,#166534);">
        <div class="py-2 px-6" style="border-bottom:1px solid rgba(22,163,74,0.4);">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
                <span class="text-green-300 text-xs font-medium hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?= $inisial ?></div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block"><?= htmlspecialchars($username) ?></span>
                </div>
            </div>
        </div>
        <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
            <a href="dashboard.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2"> <span>Dashboard</span></a>
            <a href="pembayaran.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm flex items-center gap-2"> <span>Pembayaran</span></a>
            <a href="riwayat.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2"> <span>Riwayat Bayar</span></a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200 flex items-center gap-2"> <span>Keluar</span></a>
            </div>
        </nav>
    </header>

    <?php if (!$transaksi_sukses): ?>
    <div id="page-form" class="page active">
        <form action="" method="POST" id="formPembayaran">
            <input type="hidden" name="jenis_pajak" id="inputJenis" required>
            <input type="hidden" name="nominal" id="inputNominal" required>
            <input type="hidden" name="metode" id="inputMetode" required>
            <input type="hidden" name="proses_bayar" value="1">

            <div class="max-w-3xl mx-auto px-6 py-10">
                <div class="mb-8">
                    <h1 class="font-extrabold text-4xl text-neutral-900 mb-1" style="font-family:'Montserrat',sans-serif;">Pembayaran Pajak</h1>
                    <p class="text-neutral-500 text-sm">Pilih jenis pajak dan metode pembayaran Anda</p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
                    <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                        Pilih Jenis Pajak
                    </h2>
                    <p class="text-neutral-400 text-xs mb-6 ml-9">Pilih periode pembayaran pajak kios Anda</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Harian',5000)">
                            <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                            <h3 class="font-bold text-neutral-800 mb-1 mt-3">Harian</h3>
                            <p class="text-neutral-400 text-xs mb-3">Pembayaran per hari</p>
                            <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 5.000</p>
                        </div>
                        <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Mingguan',35000)">
                            <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                            <div class="w-fit mb-2 px-2 py-0.5 bg-yellow-400 text-yellow-900 text-[10px] font-bold rounded-full uppercase tracking-wider mt-3">Populer</div>
                            <h3 class="font-bold text-neutral-800 mb-1">Mingguan</h3>
                            <p class="text-neutral-400 text-xs mb-3">Pembayaran per minggu</p>
                            <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 35.000</p>
                        </div>
                        <div class="jenis-card border-2 border-neutral-200 rounded-2xl p-5 relative" onclick="pilihJenis(this,'Bulanan',140000)">
                            <div class="jenis-check absolute top-3 right-3 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold">✓</div>
                            <div class="w-fit mb-2 px-2 py-0.5 bg-green-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider mt-3">Hemat</div>
                            <h3 class="font-bold text-neutral-800 mb-1">Bulanan</h3>
                            <p class="text-neutral-400 text-xs mb-3">Pembayaran per bulan</p>
                            <p class="font-extrabold text-2xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp 140.000</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
                    <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">2</span>
                        Nomor Kios
                    </h2>
                    <p class="text-neutral-400 text-xs mb-5 ml-9">Masukkan nomor kios yang akan dibayarkan</p>
                    <input type="text" name="nomor_kios" id="nomorKios" 
       value="<?= htmlspecialchars($kios_default) ?>" 
       readonly 
       class="w-full border-2 border-neutral-200 bg-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-green-500 transition-all duration-200 cursor-not-allowed" 
       maxlength="10">
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600 mb-6">
                    <h2 class="font-bold text-lg text-neutral-800 mb-1 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center">3</span>
                        Metode Pembayaran
                    </h2>
                    <p class="text-neutral-400 text-xs mb-5 ml-9">Pilih cara pembayaran yang Anda inginkan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'DANA')">
                            <div class="w-12 h-12 bg-green-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="images/DANA.jpg" alt="DANA" class="w-full h-full object-cover">
                            </div>
                            <div><p class="font-semibold text-neutral-800 text-sm">Dana</p><p class="text-neutral-400 text-xs">Bayar menggunakan saldo DANA anda</p></div>
                        </div>
                        <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'Transfer Bank')">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="images/bank.jpg" alt="Bank" class="w-full h-full object-cover">
                            </div>
                            <div><p class="font-semibold text-neutral-800 text-sm">Transfer Bank</p><p class="text-neutral-400 text-xs">BRI / BNI / Mandiri</p></div>
                        </div>
                        <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetodeQRIS(this)">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="images/QRIS.jpg" alt="QRIS" class="w-full h-full object-cover">
                            </div>
                            <div><p class="font-semibold text-neutral-800 text-sm">QRIS</p><p class="text-neutral-400 text-xs">Scan QR dari dompet digital</p></div>
                        </div>
                        <div class="metode-card border-2 border-neutral-200 rounded-xl p-4 flex items-center gap-4" onclick="pilihMetode(this,'Gopay')">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="images/Gopay.jpg" alt="Gopay" class="w-full h-full object-cover">
                            </div>
                            <div><p class="font-semibold text-neutral-800 text-sm">GoPay</p><p class="text-neutral-400 text-xs">Bayar menggunakan saldo Gopay anda</p></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-yellow-400 mb-8">
                    <h2 class="font-bold text-lg text-neutral-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-yellow-400 text-yellow-900 text-xs font-bold flex items-center justify-center">✦</span>
                        Ringkasan Pembayaran
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm"><span class="text-neutral-500">Nomor Kios</span><span class="font-semibold text-neutral-800" id="sum-kios">-</span></div>
                        <div class="flex justify-between text-sm"><span class="text-neutral-500">Jenis Pajak</span><span class="font-semibold text-neutral-800" id="sum-jenis">-</span></div>
                        <div class="flex justify-between text-sm"><span class="text-neutral-500">Metode Bayar</span><span class="font-semibold text-neutral-800" id="sum-metode">-</span></div>
                        <div class="border-t border-neutral-100 pt-3 flex justify-between items-center">
                            <span class="font-bold text-neutral-800">Total Tagihan</span>
                            <span class="font-extrabold text-2xl text-green-600" id="sum-total" style="font-family:'Montserrat',sans-serif;">Rp 0</span>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="validasiDanKirim()"
                        class="w-full py-4 text-white font-bold text-lg rounded-2xl hover:shadow-xl hover:shadow-green-200 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3"
                        style="background:linear-gradient(to right,#15803d,#16a34a);">
                    <span>Bayar Sekarang</span>
                </button>
                <p class="text-center text-neutral-400 text-xs mt-4">🔒 Transaksi dijamin aman dan terenkripsi</p>
            </div>
        </form>
    </div>
    
    <?php else: ?>
    <div id="page-sukses" class="page active">
        <div class="max-w-lg mx-auto px-6 py-14">
            <div class="text-center mb-8">
                <div class="anim-pop w-28 h-28 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-green-300" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="anim-fade delay-1 font-extrabold text-3xl text-neutral-900 mt-5" style="font-family:'Montserrat',sans-serif;">Pembayaran Berhasil!</h1>
                <p class="anim-fade delay-2 text-neutral-500 text-sm mt-2">Terima kasih, transaksi Anda telah berhasil diproses.</p>
            </div>

            <div class="anim-fade delay-2 bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="p-6 text-center" style="background:linear-gradient(135deg,#166534,#15803d);">
                    <p class="text-green-300 text-xs tracking-widest uppercase mb-1">Bukti Pembayaran</p>
                    <p class="text-yellow-400 font-extrabold text-xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</p>
                    <p class="text-green-300 text-xs mt-1"><?= $data_sukses['tanggal'] ?></p>
                </div>
                <div class="flex items-center px-4">
                    <div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -ml-2.5"></div>
                    <div class="flex-1 border-t-2 border-dashed border-neutral-200 mx-2"></div>
                    <div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -mr-2.5"></div>
                </div>
                <div class="px-7 py-5 space-y-3.5">
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">No. Transaksi</span><span class="font-semibold text-neutral-700 font-mono text-xs"><?= $data_sukses['no_trx'] ?></span></div>
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Nama Pemilik</span><span class="font-semibold text-neutral-800"><?= htmlspecialchars($username) ?></span></div>
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Nomor Kios</span><span class="font-semibold text-neutral-800"><?= htmlspecialchars($data_sukses['kios']) ?></span></div>
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Jenis Pajak</span><span class="font-semibold text-neutral-800">Pajak <?= htmlspecialchars($data_sukses['jenis']) ?></span></div>
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Metode Bayar</span><span class="font-semibold text-neutral-800"><?= htmlspecialchars($data_sukses['metode']) ?></span></div>
                    <div class="flex justify-between items-center text-sm"><span class="text-neutral-400">Status</span><span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">✓ Berhasil</span></div>
                </div>
                <div class="flex items-center px-4">
                    <div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -ml-2.5"></div>
                    <div class="flex-1 border-t-2 border-dashed border-neutral-200 mx-2"></div>
                    <div class="w-5 h-5 rounded-full bg-green-50 flex-shrink-0 -mr-2.5"></div>
                </div>
                <div class="px-7 py-5 bg-green-50 text-center">
                    <p class="text-neutral-500 text-xs mb-1">Total Dibayarkan</p>
                    <p class="font-extrabold text-4xl text-green-600" style="font-family:'Montserrat',sans-serif;">Rp <?= number_format($data_sukses['nominal'], 0, ',', '.') ?></p>
                </div>
            </div>

            <div class="anim-fade delay-3 grid grid-cols-2 gap-3 mt-6">
                <a href="riwayat.php"
                   class="py-3.5 border-2 border-green-600 text-green-700 rounded-2xl font-semibold text-sm text-center hover:bg-green-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                     Riwayat Pembayaran
                </a>
                <a href="pembayaran.php"
                   class="py-3.5 bg-white border-2 border-neutral-200 text-neutral-700 rounded-2xl text-center font-semibold text-sm hover:border-green-500 hover:text-green-600 transition-all duration-300 flex items-center justify-center">
                     Bayar Lagi
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div id="qrisOverlay" class="overlay fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white rounded-3xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl">
            <h3 class="font-bold text-xl text-neutral-800 mb-1" style="font-family:'Montserrat',sans-serif;">Scan QRIS</h3>
            <p class="text-neutral-400 text-sm mb-5">Scan QR code menggunakan dompet digital Anda</p>
            <div class="w-48 h-48 mx-auto border-4 border-neutral-800 rounded-2xl flex items-center justify-center mb-5 p-3 bg-white">
                <svg viewBox="0 0 100 100" class="w-full h-full">
                    <rect x="5"  y="5"  width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                    <rect x="11" y="11" width="16" height="16" fill="#1f2937"/>
                    <rect x="67" y="5"  width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                    <rect x="73" y="11" width="16" height="16" fill="#1f2937"/>
                    <rect x="5"  y="67" width="28" height="28" fill="none" stroke="#1f2937" stroke-width="4"/>
                    <rect x="11" y="73" width="16" height="16" fill="#1f2937"/>
                    <rect x="40" y="5"  width="6"  height="6"  fill="#1f2937"/>
                    <rect x="52" y="5"  width="6"  height="6"  fill="#1f2937"/>
                    <rect x="40" y="17" width="6"  height="6"  fill="#1f2937"/>
                    <rect x="52" y="17" width="12" height="6"  fill="#1f2937"/>
                    <rect x="40" y="40" width="6"  height="24" fill="#1f2937"/>
                    <rect x="52" y="40" width="6"  height="6"  fill="#1f2937"/>
                    <rect x="64" y="40" width="6"  height="12" fill="#1f2937"/>
                    <rect x="76" y="40" width="12" height="6"  fill="#1f2937"/>
                    <rect x="52" y="52" width="12" height="6"  fill="#1f2937"/>
                    <rect x="76" y="52" width="6"  height="18" fill="#1f2937"/>
                    <rect x="40" y="70" width="18" height="6"  fill="#1f2937"/>
                    <rect x="64" y="70" width="6"  height="18" fill="#1f2937"/>
                    <rect x="52" y="82" width="6"  height="12" fill="#1f2937"/>
                    <rect x="76" y="76" width="12" height="6"  fill="#1f2937"/>
                </svg>
            </div>
            <div class="bg-green-50 rounded-xl p-3 mb-3">
                <p class="text-green-700 text-xs font-medium">Total yang harus dibayar:</p>
                <p class="text-green-600 font-extrabold text-2xl" id="qrisAmount" style="font-family:'Montserrat',sans-serif;">Rp 0</p>
            </div>
            <p class="text-neutral-400 text-xs mb-5">QR berlaku selama <span class="text-green-600 font-semibold" id="qrisTimer">05:00</span></p>
            <div class="flex gap-3">
                <button onclick="tutupQRIS()" class="flex-1 py-2.5 border-2 border-neutral-200 rounded-xl text-sm font-semibold text-neutral-600 hover:border-red-300 hover:text-red-500 transition">Batal</button>
                <button onclick="konfirmasiQRIS()" class="flex-1 py-2.5 text-white rounded-xl text-sm font-semibold transition" style="background:linear-gradient(to right,#15803d,#16a34a);">✓ Sudah Bayar</button>
            </div>
        </div>
    </div>

    <footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10">
        &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar. All rights reserved.
    </footer>

    <script>
        let selectedJenis   = null;
        let selectedNominal = 0;
        let selectedMetode  = null;
        let qrisInterval    = null;
        let qrisMetodeEl    = null;

        window.onload = function () {
            // Pasang event listener untuk mengetik nomor kios agar ringkasan ter-update
            const inputKios = document.getElementById('nomorKios');
            if (inputKios) {
                inputKios.addEventListener('input', updateRingkasan);
            }
            updateRingkasan();
        };

        // Memilih Jenis Pajak
        function pilihJenis(el, jenis, nominal) {
            document.querySelectorAll('.jenis-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            selectedJenis   = jenis;
            selectedNominal = nominal;
            
            // Set input tersembunyi untuk PHP
            document.getElementById('inputJenis').value = jenis;
            document.getElementById('inputNominal').value = nominal;
            
            updateRingkasan();
        }

        // Memilih Metode Pembayaran
        function pilihMetode(el, metode) {
            document.querySelectorAll('.metode-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            selectedMetode = metode;
            
            // Set input tersembunyi untuk PHP
            document.getElementById('inputMetode').value = metode;
            
            updateRingkasan();
        }

        // QRIS popup
        function pilihMetodeQRIS(el) {
            if (!selectedNominal) { alert('Pilih jenis pajak terlebih dahulu!'); return; }
            qrisMetodeEl = el;
            document.getElementById('qrisAmount').innerText = 'Rp ' + selectedNominal.toLocaleString('id-ID');
            document.getElementById('qrisOverlay').classList.add('show');
            startQrisTimer(300);
        }
        function tutupQRIS() {
            document.getElementById('qrisOverlay').classList.remove('show');
            clearInterval(qrisInterval);
        }
        function konfirmasiQRIS() {
            tutupQRIS();
            pilihMetode(qrisMetodeEl, 'QRIS');
        }
        function startQrisTimer(seconds) {
            clearInterval(qrisInterval);
            let rem = seconds;
            const tick = () => {
                const m = String(Math.floor(rem/60)).padStart(2,'0');
                const s = String(rem%60).padStart(2,'0');
                document.getElementById('qrisTimer').innerText = `${m}:${s}`;
                if (rem <= 0) clearInterval(qrisInterval);
                rem--;
            };
            tick();
            qrisInterval = setInterval(tick, 1000);
        }

        // Update Text Ringkasan UI
        function updateRingkasan() {
            const kios = document.getElementById('nomorKios').value || '-';
            document.getElementById('sum-kios').innerText   = kios;
            document.getElementById('sum-jenis').innerText  = selectedJenis  || '-';
            document.getElementById('sum-metode').innerText = selectedMetode || '-';
            document.getElementById('sum-total').innerText  = selectedNominal ? 'Rp ' + selectedNominal.toLocaleString('id-ID') : 'Rp 0';
        }

        // Validasi JavaScript sebelum Form Submit ke PHP
        function validasiDanKirim() {
            const kios = document.getElementById('nomorKios').value.trim();
            if (!kios)           { alert('Masukkan nomor kios terlebih dahulu!'); document.getElementById('nomorKios').focus(); return; }
            if (!selectedJenis)  { alert('Pilih jenis pajak terlebih dahulu!'); return; }
            if (!selectedMetode) { alert('Pilih metode pembayaran terlebih dahulu!'); return; }

            // Lolos validasi, kirim form ke PHP
            document.getElementById('formPembayaran').submit();
        }
    </script>
</body>
</html>