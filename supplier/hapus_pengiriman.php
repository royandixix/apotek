<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil data pengiriman
    $stmt = $conn->prepare("SELECT * FROM pengiriman_obat_222233 WHERE pengiriman_id_222233 = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pengiriman = $result->fetch_assoc();
    $stmt->close();

    if (!$pengiriman) {
        header("Location: pengiriman_obat.php?message=Pengiriman tidak ditemukan");
        exit();
    }

    $status = strtolower(trim($pengiriman['status_pengiriman_222233']));
    $valid_status = ['diproses', 'dikirim', 'diterima'];
    $obat_id = $pengiriman['obat_id_222233'];
    $jumlah = (int)$pengiriman['jumlah_222233'];

    // Jika ada obat_id dan status pengiriman valid, lakukan update stok
    if (!is_null($obat_id) && in_array($status, $valid_status)) {
        // Ambil stok saat ini
        $stmt = $conn->prepare("SELECT stok_222233 FROM obat_222233 WHERE obat_id_222233 = ?");
        $stmt->bind_param("i", $obat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $obat = $result->fetch_assoc();
        $stmt->close();

        if ($obat) {
            $stok_sekarang = (int)$obat['stok_222233'];
            $stok_baru = $stok_sekarang - $jumlah;

            if ($stok_baru < 0) {
                header("Location: pengiriman_obat.php?message=Gagal: stok menjadi negatif");
                exit();
            }

            // Update stok
            $stmt = $conn->prepare("UPDATE obat_222233 SET stok_222233 = ? WHERE obat_id_222233 = ?");
            $stmt->bind_param("ii", $stok_baru, $obat_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Hapus data pengiriman
    $stmt = $conn->prepare("DELETE FROM pengiriman_obat_222233 WHERE pengiriman_id_222233 = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: pengiriman_obat.php?message=Pengiriman berhasil dihapus");
    } else {
        header("Location: pengiriman_obat.php?message=Gagal menghapus pengiriman");
    }
    $stmt->close();
}
?>
