<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    die("Akses ditolak.");
}

// Ambil ID transaksi dari GET
if (!isset($_GET['transaksi_id'])) {
    die("ID transaksi tidak ditemukan.");
}

$transaksi_id = $_GET['transaksi_id'];

// Ambil detail transaksi dan pembayaran
$stmt = $conn->prepare("
    SELECT t.*, p.metode_pembayaran_222233, p.jumlah_pembayaran_222233, 
           p.status_pembayaran_222233, p.tanggal_pembayaran_222233,
           u.nama_222233 AS nama_kasir
    FROM transaksi_222233 t
    LEFT JOIN pembayaran_222233 p ON t.transaksi_id_222233 = p.transaksi_id_222233
    LEFT JOIN kasir_222233 k ON t.kasir_id_222233 = k.kasir_id_222233
    LEFT JOIN users_222233 u ON k.user_id_222233 = u.user_id_222233
    WHERE t.transaksi_id_222233 = ?
");
$stmt->bind_param("i", $transaksi_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transaksi tidak ditemukan.");
}

$transaksi = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .resi-container { max-width: 600px; margin: auto; border: 1px solid #ccc; padding: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .value { display: inline-block; }
        .line { margin-bottom: 10px; }
        .print-btn { display: block; margin: 20px auto 0; padding: 10px 20px; background-color: teal; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body onload="window.print()">

<div class="resi-container">
    <h2>Resi Transaksi Pembelian</h2>

    <div class="line"><span class="label">ID Transaksi:</span> <span class="value"><?= $transaksi['transaksi_id_222233']; ?></span></div>
    <div class="line"><span class="label">Tanggal Transaksi:</span> <span class="value"><?= $transaksi['tanggal_transaksi_222233']; ?></span></div>
    <div class="line"><span class="label">Total Harga:</span> <span class="value">Rp <?= number_format($transaksi['total_harga_222233'], 2, ',', '.'); ?></span></div>
    <div class="line"><span class="label">Status Transaksi:</span> <span class="value"><?= ucfirst($transaksi['status_transaksi_222233']); ?></span></div>

    <hr style="margin: 20px 0;">

    <div class="line"><span class="label">Metode Pembayaran:</span> <span class="value"><?= ucfirst($transaksi['metode_pembayaran_222233'] ?? '-'); ?></span></div>
    <div class="line"><span class="label">Jumlah Pembayaran:</span> <span class="value">Rp <?= number_format($transaksi['jumlah_pembayaran_222233'], 2, ',', '.'); ?></span></div>
    <div class="line"><span class="label">Status Pembayaran:</span> <span class="value"><?= ucfirst(str_replace('_', ' ', $transaksi['status_pembayaran_222233'])); ?></span></div>
    <div class="line"><span class="label">Tanggal Bayar:</span> <span class="value"><?= $transaksi['tanggal_pembayaran_222233'] ?? '-'; ?></span></div>

    <hr style="margin: 20px 0;">

    <div class="line"><span class="label">Kasir:</span> <span class="value"><?= $transaksi['nama_kasir'] ?? '-'; ?></span></div>

    <button class="print-btn" onclick="window.print()">Cetak</button>
</div>

</body>
</html>
