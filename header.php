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
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        heading: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .row-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .row-hover:hover { transform: translateX(5px); }

        /* Popup bukti */
        .popup-overlay { display: none; }
        .popup-overlay.show { display: flex; }

        /* Print / simpan style */
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
                <span class="font-extrabold text-yellow-400 text-2xl tracking-widest drop-shadow-sm" style="font-family:'Montserrat',sans-serif;">
                    SIPESEL
                </span>
                <span class="text-green-300 text-xs font-medium tracking-wide hidden sm:block">
                    Sistem Informasi Pembayaran Pajak Pasar
                </span>
                <div class="flex items-center gap-2">
                    <div id="avatarInitial" class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-green-900 font-bold text-sm">?</div>
                    <span id="navUsername" class="text-green-100 text-sm font-medium hidden sm:block">Pengguna</span>
                </div>
            </div>
        </div>
        <nav class="max-w-6xl mx-auto px-6 flex items-center gap-1 py-2">
            <a href="dashboard.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2">
                 <span>Dashboard</span>
            </a>
            <a href="pembayaran.php" class="px-4 py-2 rounded-lg text-green-200 hover:bg-green-600/50 hover:text-white text-sm font-medium transition-all duration-200 flex items-center gap-2">
                 <span>Pembayaran</span>
            </a>
            <a href="riwayat.php" class="px-4 py-2 rounded-lg bg-yellow-400/20 border border-yellow-400/50 text-yellow-300 font-semibold text-sm flex items-center gap-2">
                 <span>Riwayat Bayar</span>
            </a>
            <div class="ml-auto">
                <a href="login.php" class="px-4 py-2 rounded-lg bg-red-500/80 hover:bg-red-500 text-white text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                     <span>Keluar</span>
                </a>
            </div>
        </nav>
    </header>