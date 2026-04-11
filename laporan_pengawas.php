<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pengawas') {
    header("location:login.php?pesan=akses_ditolak");
    exit();
}

$username = $_SESSION['username'];
$inisial  = strtoupper(substr($username, 0, 1));
$sekarang = new DateTime();

// ── Ambil semua pedagang beserta transaksi terakhir mereka ─────────────────
// Subquery: ambil transaksi approved terakhir per pedagang
$sql = "
    SELECT
        u.id_user,
        u.nama,
        u.username,
        u.no_kios,
        u.nomor_hp,
        t.no_trx,
        t.jenis_pajak,
        t.nominal,
        t.tanggal        AS tanggal_bayar,
        t.status         AS status_trx,
        -- hitung jatuh tempo berdasarkan jenis pajak
        CASE
            WHEN t.jenis_pajak = 'Harian'   THEN DATE_ADD(t.tanggal, INTERVAL 1  DAY)
            WHEN t.jenis_pajak = 'Mingguan' THEN DATE_ADD(t.tanggal, INTERVAL 7  DAY)
            WHEN t.jenis_pajak = 'Bulanan'  THEN DATE_ADD(t.tanggal, INTERVAL 30 DAY)
            ELSE NULL
        END AS jatuh_tempo
    FROM users u
    LEFT JOIN transaksi t ON t.id_transaksi = (
        SELECT id_transaksi FROM transaksi
        WHERE id_user = u.id_user AND status = 'approved'
        ORDER BY tanggal DESC
        LIMIT 1
    )
    WHERE u.role = 'pedagang'
    ORDER BY u.nama ASC
";

$result  = mysqli_query($conn, $sql);
$pedagang_list = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Hitung sisa hari / hari terlambat
        if (!empty($row['jatuh_tempo'])) {
            $jt   = new DateTime($row['jatuh_tempo']);
            $diff = (int)$sekarang->diff($jt)->days;
            $row['sudah_lewat'] = ($jt < $sekarang);
            $row['selisih_hari'] = $diff;
        } else {
            $row['sudah_lewat']   = true;   // belum pernah bayar = sudah telat
            $row['selisih_hari']  = null;
        }
        $pedagang_list[] = $row;
    }
}

// Hitung ringkasan
$total_pedagang  = count($pedagang_list);
$sudah_jatuh     = count(array_filter($pedagang_list, fn($p) => $p['sudah_lewat']));
$belum_jatuh     = $total_pedagang - $sudah_jatuh;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran - SIPESEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        #toastBox { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
    </style>
</head>

<body style="background-image: url('images/kios2.jpg');" class="relative min-h-screen text-white bg-cover bg-center">
    <div class="absolute inset-0 bg-black/30"></div>

    <!-- Header -->
    <header class="bg-green-800 shadow-xl sticky top-0 z-50">
        <div class="border-b border-green-600/50 py-2 px-6">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">SIPESEL</span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">Sistem Informasi Pembayaran Pajak Pasar</span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm"><?= $inisial ?></div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block"><?= htmlspecialchars($username) ?></span>
                </div>
            </div>
        </div>
        <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
            <a href="dashboard pengawas.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Dashboard</span></a>
            <a href="monitoring_pengawas.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Monitoring</span></a>
            <a href="laporan_pengawas.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm"><span>Laporan</span></a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200"><span>Keluar</span></a>
            </div>
        </nav>
    </header>

    <!-- Main -->
    <section class="relative z-10 px-6 md:px-20 py-8 min-h-screen">
        <div class="max-w-6xl mx-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold mb-1">Laporan Pembayaran</h1>
                <p class="text-sm opacity-80">Status tagihan dan jatuh tempo seluruh pedagang terdaftar</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20">
                    <p class="text-sm text-green-200 mb-1">Total Pedagang</p>
                    <p class="text-3xl font-bold" style="font-family:'Montserrat',sans-serif;"><?= $total_pedagang ?></p>
                </div>
                <div class="bg-green-500/20 backdrop-blur-md rounded-xl p-5 border border-green-400/30">
                    <p class="text-sm text-green-200 mb-1">Belum Jatuh Tempo</p>
                    <p class="text-3xl font-bold text-green-300" style="font-family:'Montserrat',sans-serif;"><?= $belum_jatuh ?></p>
                </div>
                <div class="bg-red-500/20 backdrop-blur-md rounded-xl p-5 border border-red-400/30">
                    <p class="text-sm text-red-200 mb-1">Sudah Jatuh Tempo / Belum Bayar</p>
                    <p class="text-3xl font-bold text-red-300" style="font-family:'Montserrat',sans-serif;"><?= $sudah_jatuh ?></p>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input id="searchInput" type="text" oninput="filterTabel()" placeholder="Cari nama pedagang atau nomor kios..."
                            class="w-full px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
                    </div>
                    <select id="filterStatus" onchange="filterTabel()"
                        class="px-4 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
                        <option value="semua">Semua Status</option>
                        <option value="jatuh_tempo">Sudah Jatuh Tempo</option>
                        <option value="aman">Belum Jatuh Tempo</option>
                    </select>
                </div>
            </div>

            <!-- Tabel -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="tabelLaporan">
                        <thead>
                            <tr class="bg-green-800/50 border-b border-white/20">
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Pedagang</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Nomor Kios</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Jenis Pajak</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Terakhir Bayar</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Jatuh Tempo</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Status</th>
                                <th class="px-5 py-4 text-left text-sm font-semibold whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pedagang_list as $p):
                            $lewat     = $p['sudah_lewat'];
                            $selisih   = $p['selisih_hari'];
                            $belumBayar = empty($p['tanggal_bayar']); // belum pernah ada transaksi approved
                        ?>
                        <tr class="baris border-b border-white/10 hover:bg-white/5 transition-all"
                            data-status="<?= ($lewat || $belumBayar) ? 'jatuh_tempo' : 'aman' ?>"
                            data-nama="<?= strtolower(htmlspecialchars($p['nama'])) ?>"
                            data-kios="<?= strtolower(htmlspecialchars($p['no_kios'] ?? '')) ?>">

                            <!-- Pedagang -->
                            <td class="px-5 py-4">
                                <p class="font-semibold text-sm"><?= htmlspecialchars($p['nama']) ?></p>
                                <p class="text-xs text-white/50">@<?= htmlspecialchars($p['username']) ?></p>
                            </td>

                            <!-- Kios -->
                            <td class="px-5 py-4 text-sm">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/30 rounded-lg text-xs">
                                    <?= htmlspecialchars($p['no_kios'] ?? '-') ?>
                                </span>
                            </td>

                            <!-- Jenis Pajak -->
                            <td class="px-5 py-4 text-sm">
                                <?php if ($p['jenis_pajak']): ?>
                                <?php
                                    $jenisBadge = [
                                        'Harian'   => 'bg-sky-500/30 text-sky-200',
                                        'Mingguan' => 'bg-purple-500/30 text-purple-200',
                                        'Bulanan'  => 'bg-indigo-500/30 text-indigo-200',
                                    ];
                                    $jc = $jenisBadge[$p['jenis_pajak']] ?? 'bg-white/20 text-white';
                                ?>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium <?= $jc ?>">
                                        <?= htmlspecialchars($p['jenis_pajak']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-white/40 text-xs">Belum ada</span>
                                <?php endif; ?>
                            </td>

                            <!-- Terakhir Bayar -->
                            <td class="px-5 py-4 text-sm text-white/80">
                                <?= $p['tanggal_bayar'] ? date('d M Y', strtotime($p['tanggal_bayar'])) : '<span class="text-white/40">Belum pernah</span>' ?>
                            </td>

                            <!-- Jatuh Tempo -->
                            <td class="px-5 py-4 text-sm">
                                <?php if (!empty($p['jatuh_tempo'])): ?>
                                    <p class="font-medium <?= $lewat ? 'text-red-300' : 'text-green-300' ?>">
                                        <?= date('d M Y', strtotime($p['jatuh_tempo'])) ?>
                                    </p>
                                    <p class="text-xs <?= $lewat ? 'text-red-400' : 'text-green-400' ?>">
                                        <?= $lewat ? 'Terlambat '.$selisih.' hari' : 'Sisa '.$selisih.' hari' ?>
                                    </p>
                                <?php else: ?>
                                    <span class="text-red-300 text-xs font-medium">Belum ada pembayaran</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 text-sm">
                                <?php if ($belumBayar || $lewat): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/30 text-red-300 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Jatuh Tempo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/30 text-green-300 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Aman
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">

                                    <!-- Tombol WhatsApp Web -->
                                    <?php
                                        // Format nomor: hilangkan 0 depan, ganti dengan 62
                                        $hp  = $p['nomor_hp'] ?? '';
                                        $hp  = preg_replace('/\D/', '', $hp); // hapus non-digit
                                        if (str_starts_with($hp, '0')) $hp = '62' . substr($hp, 1);
                                        $pesan = urlencode(
                                            "Yth. Bapak/Ibu " . $p['nama'] . ",\n" .
                                            "Kami dari pengelola Pasar ingin mengingatkan bahwa tagihan pajak kios " . ($p['no_kios'] ?? '-') . " Anda telah jatuh tempo.\n" .
                                            "Mohon segera lakukan pembayaran melalui aplikasi SIPESEL.\n\n" .
                                            "Terima kasih."
                                        );
                                        $waUrl = "https://web.whatsapp.com/send?phone={$hp}&text={$pesan}";
                                    ?>
                                    <a href="<?= $waUrl ?>" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-600/40 hover:bg-green-600/60 border border-green-500/50 rounded-lg text-xs font-semibold transition-all"
                                        title="Hubungi via WhatsApp Web">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.554 4.122 1.524 5.856L0 24l6.306-1.501A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.879 9.879 0 01-5.034-1.376l-.36-.214-3.742.981 1.001-3.648-.235-.375A9.834 9.834 0 012.118 12C2.118 6.542 6.542 2.118 12 2.118c5.457 0 9.882 4.424 9.882 9.882 0 5.457-4.425 9.882-9.882 9.882z"/>
                                        </svg>
                                        WhatsApp
                                    </a>

                                    <!-- Tombol Kirim Notifikasi — hanya aktif jika jatuh tempo -->
                                    <?php if ($belumBayar || $lewat): ?>
                                    <button
                                        onclick="kirimNotifikasi(<?= $p['id_user'] ?>, '<?= htmlspecialchars($p['nama']) ?>', '<?= htmlspecialchars($p['no_kios'] ?? '-') ?>', this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-yellow-500/30 hover:bg-yellow-500/50 border border-yellow-500/50 rounded-lg text-xs font-semibold transition-all"
                                        title="Kirim notifikasi ke dashboard pedagang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        Notifikasi
                                    </button>
                                    <?php else: ?>
                                    <button disabled
                                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-xs font-semibold text-white/30 cursor-not-allowed"
                                        title="Pedagang belum jatuh tempo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        Notifikasi
                                    </button>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($pedagang_list)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-white/40 text-sm">
                                Belum ada data pedagang terdaftar.
                            </td>
                        </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <!-- Toast Container -->
    <div id="toastBox"></div>

    <footer class="relative z-10 bg-green-800 text-green-300 text-center text-xs py-4 mt-10">
        &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar. All rights reserved.
    </footer>

    <script>
        // ── Filter tabel ─────────────────────────────────────────────────────
        function filterTabel() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            const status  = document.getElementById('filterStatus').value;
            document.querySelectorAll('#tabelLaporan .baris').forEach(row => {
                const namaMatch  = row.dataset.nama.includes(keyword) || row.dataset.kios.includes(keyword);
                const statusMatch = status === 'semua' || row.dataset.status === status;
                row.style.display = (namaMatch && statusMatch) ? '' : 'none';
            });
        }

        // ── Kirim Notifikasi via AJAX ─────────────────────────────────────────
        function kirimNotifikasi(idUser, nama, kios, btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Mengirim...`;

            const formData = new FormData();
            formData.append('id_user', idUser);
            formData.append('nama',    nama);
            formData.append('kios',    kios);

            fetch('kirim_notifikasi.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    tampilToast('Notifikasi berhasil dikirim ke ' + nama, 'success');
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Terkirim`;
                    btn.classList.replace('bg-yellow-500/30', 'bg-green-500/30');
                    btn.classList.replace('border-yellow-500/50', 'border-green-500/50');
                } else {
                    tampilToast('Gagal mengirim notifikasi: ' + (data.message || ''), 'error');
                    btn.disabled = false;
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg> Notifikasi`;
                }
            })
            .catch(() => {
                tampilToast('Terjadi kesalahan jaringan', 'error');
                btn.disabled = false;
            });
        }

        // ── Toast ─────────────────────────────────────────────────────────────
        function tampilToast(pesan, tipe) {
            const box   = document.getElementById('toastBox');
            const toast = document.createElement('div');
            const warna = tipe === 'success'
                ? 'bg-green-600 border-green-400'
                : 'bg-red-600 border-red-400';
            toast.className = `px-5 py-3 rounded-xl border text-white text-sm font-medium shadow-xl ${warna} transition-all duration-300`;
            toast.textContent = pesan;
            box.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
        }
    </script>
</body>
</html>