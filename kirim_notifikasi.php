<?php
session_start();
require_once 'koneksi.php';

header('Content-Type: application/json');

// Hanya bisa diakses pengawas
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pengawas') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak valid']);
    exit();
}

$id_user_tujuan = (int)$_POST['id_user'];
$nama           = mysqli_real_escape_string($conn, $_POST['nama']);
$kios           = mysqli_real_escape_string($conn, $_POST['kios']);
$id_pengirim    = $_SESSION['id_user'];

if (!$id_user_tujuan) {
    echo json_encode(['success' => false, 'message' => 'ID user tidak valid']);
    exit();
}

$pesan = "Tagihan pajak kios {$kios} Anda telah melewati jatuh tempo. Segera lakukan pembayaran melalui menu Pembayaran.";

$sql = "INSERT INTO notifikasi (id_user, id_pengirim, pesan, dibaca, created_at)
        VALUES ($id_user_tujuan, $id_pengirim, '$pesan', 0, NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
?>