<?php
session_start();
include '../koneksi.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Pastikan ID obat diberikan
if (isset($_GET['id'])) {
    $obat_id = $_GET['id'];

    // Validasi apakah ID adalah angka
    if (!is_numeric($obat_id)) {
        die("ID tidak valid.");
    }

    // Query untuk menghapus data obat
    $sql = "DELETE FROM obat_222233 WHERE obat_id_222233 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $obat_id);

    // Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil menghapus, arahkan ke halaman daftar obat
        header("Location: obat.php?message=success");
    } else {
        // Jika gagal
        echo "Gagal menghapus data obat.";
    }
    $stmt->close();
} else {
    // Jika tidak ada ID yang diberikan
    echo "ID obat tidak ditemukan.";
}

$conn->close();
?>
