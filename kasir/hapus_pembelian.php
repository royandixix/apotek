<?php
session_start();
include '../koneksi.php';

// Pastikan user kasir dan ada parameter delete_id
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir' || !isset($_GET['delete_id'])) {
    header("Location: transaksi_pembelian.php");
    exit;
}

$transaksi_id = (int)$_GET['delete_id'];

// Hapus keranjang terlebih dahulu (foreign key)
$stmt = $conn->prepare("DELETE FROM keranjang_222233 WHERE transaksi_id_222233 = ?");
$stmt->bind_param("i", $transaksi_id);
$stmt->execute();
$stmt->close();

// Hapus transaksi
$stmt = $conn->prepare("DELETE FROM transaksi_222233 WHERE transaksi_id_222233 = ?");
$stmt->bind_param("i", $transaksi_id);

if ($stmt->execute()) {
    $_SESSION['pesan'] = '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">Transaksi berhasil dihapus.</div>';
} else {
    $_SESSION['pesan'] = '<div class="bg-red-100 text-red-800 p-4 rounded mb-4">Gagal menghapus transaksi.</div>';
}
$stmt->close();

header("Location: transaksi_pembelian.php");
exit;
?>
