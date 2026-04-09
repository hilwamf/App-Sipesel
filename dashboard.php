<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("location:login.php?pesan=login_dulu");
    exit();
}

// Ambil data dari session PHP
$username = $_SESSION['username'];
$inisial = strtoupper(substr($username, 0, 1)); // Mengambil huruf pertama untuk avatar
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - SIPESEL</title>
    <link href="./src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body
    style="background-image: url('images/kios2.jpg');"
    class="relative min-h-screen text-white bg-cover bg-center">

    <div class="absolute inset-0 bg-black/30"></div>

    <header class="bg-green-800 shadow-xl sticky top-0 z-50">
        <div class="border-b border-green-600/50 py-2 px-6">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm"
                      style="font-family: 'Montserrat', sans-serif;">
                    SIPESEL
                </span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">
                    Sistem Informasi Pembayaran Pajak Pasar
                </span>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">
                        <?php echo $inisial; ?>
                    </div>
                    <span class="text-green-100 text-sm font-medium hidden sm:block">
                        <?php echo htmlspecialchars($username); ?>
                    </span>
                </div>
            </div>
        </div>
        <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
            <a href="dashboard.php"
               class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm flex items-center gap-2">
                 <span>Dashboard</span>
            </a>
            <a href="pembayaran.php"
               class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2">
                 <span>Pembayaran</span>
            </a>
            <a href="riwayat.php"
               class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2">
                 <span>Riwayat Bayar</span>
            </a>
            <div class="ml-auto">
                <a href="logout.php"
                   class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                     <span>Keluar</span>
                </a>
            </div>
        </nav>
    </header>

    <section class="relative z-10 flex items-center h-screen px-6 md:px-20">
        <div class="max-w-xl">
            <h1 class="text-3xl md:text-5xl font-bold mb-4 md:mb-6 leading-tight">
                Sistem Pembayaran Pajak Kios
            </h1>
            
            <h2 class="text-lg mb-4 text-yellow-400">
                Halo, <?php echo htmlspecialchars($username); ?> 👋
            </h2>

            <p class="text-sm md:text-lg opacity-90 mb-6 md:mb-8">
                anda masuk sebagai pedagang
            </p>
        </div>
    </section>

</body>
</html>