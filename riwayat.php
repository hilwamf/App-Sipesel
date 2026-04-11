<?php
session_start();
require_once 'koneksi.php';

// ── Cek login ──────────────────────────────────────────────────────────────
if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$id_user  = $_SESSION['id_user'];
$username = $_SESSION['username'];
$nama     = $_SESSION['nama'] ?? $username;
$inisial  = strtoupper(substr($username, 0, 1));

// ── Query: hanya transaksi milik user yang sedang login ────────────────────
// FIX BUG 2: tambah WHERE id_user = $id_user
$sql = "SELECT 
            no_trx          AS id,
            nomor_kios      AS kios,
            jenis_pajak     AS jenis,
            metode_pembayaran AS metode,
            nominal         AS jumlah,
            tanggal,
            status,
            catatan_verifikasi
        FROM transaksi
        WHERE id_user = $id_user
        ORDER BY id_transaksi DESC";

$result = mysqli_query($conn, $sql);
$data_transaksi = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['jumlah'] = (int)$row['jumlah'];
        $data_transaksi[] = $row;
    }
}
$json_transaksi = json_encode($data_transaksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - SIPESEL</title>
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
        .row-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .row-hover:hover { transform: translateX(5px); }
        .popup-overlay { display: none; }
        .popup-overlay.show { display: flex; }
        @media print {
            body > *:not(#printArea) { display: none !important; }
            #printArea { display: block !important; position: fixed; inset: 0; padding: 20px; background: white; }
        }
    </style>
</head>
<body class="bg-green-50 text-neutral-900 min-h-screen">

    <header class="shadow-xl sticky top-0 z-50" style="background: linear-gradient(to right, #166534, #15803d, #166534);">
        <div class="py-2 px-6" style="border-bottom: 1px solid rgba(22,163,74,0.4);">
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
            <a href="dashboard.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Dashboard</span></a>
            <a href="pembayaran.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200"><span>Pembayaran</span></a>
            <a href="riwayat.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm"><span>Riwayat Bayar</span></a>
            <div class="ml-auto">
                <a href="logout.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200"><span>Keluar</span></a>
            </div>
        </nav>
    </header>

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 mb-10">
            <div>
                <h1 class="font-extrabold text-4xl text-neutral-900" style="font-family:'Montserrat',sans-serif;">Riwayat Pembayaran</h1>
                <p class="text-neutral-500 text-sm mt-1">Halo, <span class="font-semibold text-green-700"><?= htmlspecialchars($nama) ?></span> — menampilkan transaksi kios Anda</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <button onclick="filterHistory('all', this)"
                    class="filter-btn active-filter px-5 py-2.5 rounded-xl font-semibold text-sm border-2 border-green-500 bg-gradient-to-br from-green-600 to-green-400 text-white transition-all duration-300">
                    Semua
                </button>
                <button onclick="filterHistory('approved', this)"
                    class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm border-2 border-neutral-200 bg-white text-neutral-700 hover:border-green-500 hover:text-green-600 transition-all duration-300">
                    Berhasil
                </button>
                <button onclick="filterHistory('pending', this)"
                    class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm border-2 border-neutral-200 bg-white text-neutral-700 hover:border-yellow-500 hover:text-yellow-600 transition-all duration-300">
                    Pending
                </button>
                <button onclick="filterHistory('rejected', this)"
                    class="filter-btn px-5 py-2.5 rounded-xl font-semibold text-sm border-2 border-neutral-200 bg-white text-neutral-700 hover:border-red-500 hover:text-red-600 transition-all duration-300">
                    Gagal
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
            <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600">
                <p class="text-neutral-500 text-sm mb-2">Total Pembayaran (Berhasil)</p>
                <p class="font-extrabold text-3xl text-neutral-900" id="statTotal" style="font-family:'Montserrat',sans-serif;">Rp 0</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-green-600">
                <p class="text-neutral-500 text-sm mb-2">Jumlah Transaksi</p>
                <p class="font-extrabold text-3xl text-neutral-900" id="statJumlah" style="font-family:'Montserrat',sans-serif;">0</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-yellow-400">
                <p class="text-neutral-500 text-sm mb-2">Menunggu Verifikasi</p>
                <p class="font-extrabold text-3xl text-yellow-600" id="statPending" style="font-family:'Montserrat',sans-serif;">0</p>
            </div>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-2xl p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <h2 class="font-bold text-2xl text-neutral-900" style="font-family:'Montserrat',sans-serif;">Daftar Transaksi</h2>
                <input type="text" oninput="searchHistory(this.value)" placeholder="Cari transaksi..."
                    class="w-full sm:w-64 px-4 py-2.5 border-2 border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-green-500 transition-all">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-left">
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Transaksi</th>
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Metode</th>
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 pb-3 text-xs font-semibold text-neutral-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>

            <div id="noData" class="hidden text-center py-16 text-neutral-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium">Belum ada transaksi</p>
            </div>
        </div>
    </div>

    <!-- Popup Bukti -->
    <div id="popupOverlay" class="popup-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center" style="background: linear-gradient(135deg, #166534, #15803d);">
                <p class="text-green-300 text-xs tracking-widest uppercase mb-1">Bukti Pembayaran</p>
                <p class="text-yellow-400 font-extrabold text-xl tracking-widest" style="font-family:'Montserrat',sans-serif;">SIPESEL</p>
                <p class="text-green-300 text-xs mt-1" id="popDate">-</p>
            </div>
            <div class="flex items-center px-4 py-1">
                <div class="flex-1 border-t-2 border-dashed border-neutral-200"></div>
            </div>
            <div class="px-7 py-5 space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-neutral-400">No. Transaksi</span><span class="font-mono font-semibold text-xs" id="popId">-</span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Nama</span><span class="font-semibold" id="popUser">-</span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Nomor Kios</span><span class="font-semibold" id="popKios">-</span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Jenis Pajak</span><span class="font-semibold" id="popJenis">-</span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Metode</span><span class="font-semibold" id="popMetode">-</span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Status</span><span id="popStatus">-</span></div>

                <!-- Catatan penolakan — tampil hanya jika rejected -->
                <div id="catatanBox" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                    <p class="text-xs text-red-500 font-semibold mb-1">Alasan Penolakan:</p>
                    <p class="text-xs text-red-700" id="popCatatan">-</p>
                </div>
            </div>
            <div class="flex items-center px-4 py-1">
                <div class="flex-1 border-t-2 border-dashed border-neutral-200"></div>
            </div>
            <div class="px-7 py-5 bg-green-50 text-center">
                <p class="text-neutral-500 text-xs mb-1">Total Dibayarkan</p>
                <p class="font-extrabold text-4xl text-green-600" id="popTotal" style="font-family:'Montserrat',sans-serif;">Rp 0</p>
            </div>
            <div class="p-4 flex gap-3">
                <button onclick="tutupPopup()" class="flex-1 py-2.5 border-2 border-neutral-200 rounded-xl text-sm font-semibold text-neutral-600 hover:bg-neutral-50 transition">Tutup</button>
                <button onclick="simpanBukti()" class="flex-1 py-2.5 text-white rounded-xl text-sm font-semibold transition" style="background: linear-gradient(to right, #15803d, #16a34a);">Simpan Bukti</button>
            </div>
        </div>
    </div>

    <div id="printArea" style="display:none;"></div>

    <footer class="bg-green-800 text-green-300 text-center text-xs py-4 mt-10">
        &copy; 2026 <span class="text-yellow-400 font-bold">SIPESEL</span> — Sistem Informasi Pembayaran Pajak Pasar. All rights reserved.
    </footer>

    <script>
        let currentTrx = null;
        let allData    = [];

        // FIX BUG 2: nama diambil dari PHP session, bukan sessionStorage JS
        const namaUser = <?= json_encode($nama) ?>;

        const metodeIcon = {
            'DANA'         : '💳',
            'Transfer Bank': '🏦',
            'QRIS'         : '📱',
            'Gopay'        : '👛',
        };

        window.onload = function () {
            // FIX BUG 2: tidak lagi pakai sessionStorage untuk username
            allData = <?= $json_transaksi ?>;
            renderTable(allData);
            updateStats(allData);
        };

        // ── FIX BUG 1: mapping status database → tampilan ──────────────────
        // Database menyimpan: 'Pending', 'approved', 'rejected'
        function statusBadgeHTML(status) {
            const s = (status || '').toLowerCase();
            if (s === 'approved' || s === 'berhasil')
                return `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Berhasil</span>`;
            if (s === 'pending')
                return `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-600">⏳ Menunggu Verifikasi</span>`;
            // rejected atau apapun selain di atas
            return `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">✗ Ditolak</span>`;
        }

        function renderTable(data) {
            const tbody = document.getElementById('historyBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                document.getElementById('noData').classList.remove('hidden');
                return;
            }
            document.getElementById('noData').classList.add('hidden');

            data.forEach(trx => {
                const icon = metodeIcon[trx.metode] || '💳';
                const row  = document.createElement('tr');

                // Simpan status asli di dataset untuk filter
                row.dataset.status = (trx.status || '').toLowerCase();
                row.className = 'row-hover bg-neutral-50 hover:bg-white hover:shadow-md rounded-xl';
                row.innerHTML = `
                    <td class="px-4 py-4 rounded-l-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-300 flex items-center justify-center text-xl flex-shrink-0">🏪</div>
                            <div>
                                <p class="font-semibold text-neutral-900 text-sm">${trx.jenis} — Kios ${trx.kios}</p>
                                <p class="text-xs text-neutral-400 font-mono">${trx.id}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-neutral-600">${trx.tanggal}</td>
                    <td class="px-4 py-4">
                        <span class="font-extrabold text-lg text-green-600" style="font-family:'Montserrat',sans-serif;">
                            Rp ${parseInt(trx.jumlah).toLocaleString('id-ID')}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-neutral-600">
                        <span class="flex items-center gap-1">${icon} ${trx.metode}</span>
                    </td>
                    <td class="px-4 py-4">${statusBadgeHTML(trx.status)}</td>
                    <td class="px-4 py-4 rounded-r-xl">
                        <button onclick='bukaPopup(${JSON.stringify(trx)})'
                            class="px-4 py-2 bg-gradient-to-br from-green-600 to-green-400 text-white text-xs font-semibold rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-green-200 transition-all duration-300">
                            Lihat Bukti
                        </button>
                    </td>`;
                tbody.appendChild(row);
            });
        }

        function updateStats(data) {
            const berhasil = data.filter(d => (d.status || '').toLowerCase() === 'approved');
            const pending  = data.filter(d => (d.status || '').toLowerCase() === 'pending');
            const total    = berhasil.reduce((s, d) => s + parseInt(d.jumlah), 0);

            document.getElementById('statTotal').innerText   = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('statJumlah').innerText  = data.length;
            document.getElementById('statPending').innerText = pending.length;
        }

        // ── FILTER — cocokkan dengan status database ──────────────────────
        function filterHistory(status, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-gradient-to-br','from-green-600','to-green-400','text-white','border-green-500');
                b.classList.add('bg-white','text-neutral-700','border-neutral-200');
            });
            btn.classList.add('bg-gradient-to-br','from-green-600','to-green-400','text-white','border-green-500');
            btn.classList.remove('bg-white','text-neutral-700','border-neutral-200');

            const rows = document.querySelectorAll('#historyBody tr');
            let visible = 0;
            rows.forEach(row => {
                const match = status === 'all' || row.dataset.status === status;
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('noData').classList.toggle('hidden', visible > 0);
        }

        function searchHistory(query) {
            const rows = document.querySelectorAll('#historyBody tr');
            const term = query.toLowerCase();
            let visible = 0;
            rows.forEach(row => {
                const match = row.textContent.toLowerCase().includes(term);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('noData').classList.toggle('hidden', visible > 0);
        }

        // ── POPUP ─────────────────────────────────────────────────────────
        function bukaPopup(trx) {
            currentTrx = trx;
            document.getElementById('popDate').innerText   = trx.tanggal;
            document.getElementById('popId').innerText     = trx.id;
            document.getElementById('popUser').innerText   = namaUser;   // FIX BUG 2
            document.getElementById('popKios').innerText   = trx.kios;
            document.getElementById('popJenis').innerText  = `Pajak ${trx.jenis}`;
            document.getElementById('popMetode').innerText = trx.metode;
            document.getElementById('popTotal').innerText  = 'Rp ' + parseInt(trx.jumlah).toLocaleString('id-ID');

            // FIX BUG 1: status label
            document.getElementById('popStatus').innerHTML = statusBadgeHTML(trx.status);

            // Tampilkan catatan penolakan jika rejected
            const catatanBox = document.getElementById('catatanBox');
            if ((trx.status || '').toLowerCase() === 'rejected' && trx.catatan_verifikasi) {
                document.getElementById('popCatatan').innerText = trx.catatan_verifikasi;
                catatanBox.classList.remove('hidden');
            } else {
                catatanBox.classList.add('hidden');
            }

            document.getElementById('popupOverlay').classList.add('show');
        }

        function tutupPopup() {
            document.getElementById('popupOverlay').classList.remove('show');
            currentTrx = null;
        }

        function simpanBukti() { window.print(); }
    </script>
</body>
</html>