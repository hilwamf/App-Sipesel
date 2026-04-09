<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sipesel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// 1. Hitung Statistik Kios
$sql_kios = "SELECT 
                k.nomor_kios, 
                MAX(CASE WHEN t.status = 'berhasil' OR t.status = 'success' THEN 1 ELSE 0 END) as sudah_bayar
            FROM kios k
            LEFT JOIN transaksi t ON k.nomor_kios = t.nomor_kios
            GROUP BY k.nomor_kios";

$res_kios = $conn->query($sql_kios);
$total_kios = 0;
$sudah_bayar = 0;
while($row = $res_kios->fetch_assoc()){
    $total_kios++;
    if($row['sudah_bayar'] == 1) $sudah_bayar++;
}
$belum_bayar = $total_kios - $sudah_bayar;

// 2. Hitung Total Pendapatan
$sql_pendapatan = "SELECT SUM(nominal) as total FROM transaksi WHERE status = 'berhasil' OR status = 'success'";
$res_pendapatan = $conn->query($sql_pendapatan);
$total_uang = $res_pendapatan->fetch_assoc()['total'] ?? 0;

// ==========================================
// PANGGIL HEADER DI SINI
// (Ini akan memuat tag <html>, <head>, <body>, dan <nav>)
// ==========================================
include 'header.php'; 
?>

    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Ringkasan Operasional</h1>
            <p class="text-gray-500">Pantau kepatuhan pembayaran retribusi pedagang secara real-time.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl mb-4">💰</div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_uang, 0, ',', '.'); ?></h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-4">🏪</div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Kios</p>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo $total_kios; ?> Kios</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center text-xl mb-4">✅</div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Sudah Bayar</p>
                <h3 class="text-2xl font-bold text-gray-800 text-green-600"><?php echo $sudah_bayar; ?></h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-xl mb-4">⚠️</div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Belum Bayar</p>
                <h3 class="text-2xl font-bold text-gray-800 text-red-600"><?php echo $belum_bayar; ?></h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                    <h3 class="font-bold text-gray-800 mb-4">Aksi Cepat Pengawasan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="pengawas_data.php" class="flex items-center p-4 border border-gray-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition group cursor-pointer">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-600 group-hover:text-white transition">📊</div>
                            <div>
                                <p class="font-bold text-gray-700 group-hover:text-blue-700 transition">Cek Status Kios</p>
                                <p class="text-xs text-gray-500">Lihat siapa yang belum bayar</p>
                            </div>
                        </a>
                        <button onclick="window.print()" class="flex items-center p-4 border border-gray-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition group text-left w-full cursor-pointer">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-indigo-600 group-hover:text-white transition">🖨️</div>
                            <div>
                                <p class="font-bold text-gray-700 group-hover:text-indigo-700 transition">Cetak Laporan</p>
                                <p class="text-xs text-gray-500">Download rekap data hari ini</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden h-full">
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                        <span>💡</span> Tips Pengawasan
                    </h3>
                    <p class="text-blue-100 text-sm leading-relaxed mb-4">
                        Lakukan pengecekan fisik ke kios yang memiliki status <span class="font-bold text-white bg-red-500/30 px-1 rounded">Belum Bayar</span> secara berkala untuk memastikan kepatuhan retribusi.
                    </p>
                </div>
                <div class="absolute -bottom-6 -right-4 text-white opacity-10 text-9xl font-bold italic pointer-events-none">
                    #
                </div>
            </div>

        </div>

    </main>

<?php 
// ==========================================
// PANGGIL FOOTER DI SINI
// (Ini akan memuat tag <footer>, </body>, dan </html>)
// ==========================================
include 'footer.php'; 
?>