<?php
include 'koneksi.php';
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipesel | Sistem Pembayaran Pajak Kios</title>
    <link href="./src/output.css" rel="stylesheet">
</head>

<body class="min-h-screen flex items-center justify-center bg-linear-to-br from-[#0d1a10] via-[#125B2A] to-[#1a3c26] text-white">

    <div class="text-center max-w-xl p-10 backdrop-blur-md bg-white/5 rounded-3xl shadow-xl">

        <!-- LOGO -->
        <img src="images/logo-sipesel.png" class="w-32 mx-auto mb-6">

        <!-- TITLE -->
        <h1 class="text-4xl font-extrabold text-yellow-400 mb-2">
            SIPESEL
        </h1>

        <h3 class="mb-4">
            Sistem Pengelolaan Pajak Kios
        </h3>

        <!-- DESKRIPSI -->
        <p class="opacity-70 mb-8 leading-relaxed">
            Solusi cerdas untuk membantu pemilik kios memantau tagihan, 
            melakukan pembayaran aman, dan melihat riwayat transaksi dalam satu platform.
        </p>

        <!-- BUTTON -->
        <div class="flex justify-center gap-4">
            <a href="login.php"
               class="bg-yellow-400 text-green-900 px-6 py-3 rounded-full font-semibold hover:scale-105 transition">
               Masuk
            </a>

            <a href="register.php"
               class="border border-white/30 px-6 py-3 rounded-full hover:bg-white/20 transition">
               Daftar
            </a>
        </div>

        <!-- FOOTER -->
        <div class="mt-8 text-sm opacity-60">
            &copy; 2026 SIPESEL
        </div>

    </div>

</body>
</html>