<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: penawaran_obat.php?error=ID tidak valid");
    exit();
}

$penawaran_id = (int)$_GET['id'];

// Cek apakah penawaran memiliki relasi di tabel pengiriman
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM pengiriman_obat_222233 WHERE penawaran_id_222233 = ?");
$stmt->bind_param("i", $penawaran_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['total'] > 0) {
    // Jika masih memiliki relasi, jangan hapus
    header("Location: penawaran_obat.php?relasi=true");
    exit();
}

// Lanjut hapus jika tidak ada relasi
$stmt = $conn->prepare("DELETE FROM penawaran_obat_222233 WHERE penawaran_id_222233 = ?");
$stmt->bind_param("i", $penawaran_id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: penawaran_obat.php?message=Penawaran berhasil dihapus");
    exit();
} else {
    $stmt->close();
    header("Location: penawaran_obat.php?error=Gagal menghapus penawaran");
    exit();
}
?>
