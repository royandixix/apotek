<?php
session_start();
include '../koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Validasi input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: kasir.php");
    exit;
}

$kasir_id = $_GET['id'];

// Ambil user_id dari kasir
$sql = "SELECT user_id_222233 FROM kasir_222233 WHERE kasir_id_222233 = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kasir_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: kasir.php");
    exit;
}

$row = $result->fetch_assoc();
$user_id = $row['user_id_222233'];

// Hapus dari tabel kasir
$delete_kasir = $conn->prepare("DELETE FROM kasir_222233 WHERE kasir_id_222233 = ?");
$delete_kasir->bind_param("i", $kasir_id);
$delete_kasir->execute();

// Hapus dari tabel users
$delete_user = $conn->prepare("DELETE FROM users_222233 WHERE user_id_222233 = ?");
$delete_user->bind_param("i", $user_id);
$delete_user->execute();

header("Location: kasir.php");
exit;
?>
