<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sipesel";
$port = 3307; // Menambahkan variabel port agar lebih rapi

// Menggunakan variabel ke dalam fungsi mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Jika berhasil
// echo "Koneksi berhasil!"; 
?>