<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Cek jika ada ID supplier yang dikirimkan melalui URL
if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    // Ambil data supplier untuk mendapatkan user_id_222233
    $sql = "SELECT user_id_222233 FROM supplier_222233 WHERE supplier_id_222233 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan, lanjutkan dengan penghapusan
    if ($result->num_rows > 0) {
        $supplier = $result->fetch_assoc();
        $user_id = $supplier['user_id_222233'];

        // Mulai transaksi untuk menghapus data
        $conn->begin_transaction();

        try {
            // Hapus data dari tabel supplier_222233
            $sql_supplier = "DELETE FROM supplier_222233 WHERE supplier_id_222233 = ?";
            $stmt_supplier = $conn->prepare($sql_supplier);
            $stmt_supplier->bind_param("i", $supplier_id);
            $stmt_supplier->execute();

            // Hapus data dari tabel users_222233
            $sql_user = "DELETE FROM users_222233 WHERE user_id_222233 = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("i", $user_id);
            $stmt_user->execute();

            // Commit transaksi jika berhasil
            $conn->commit();

            // Redirect ke halaman manajemen supplier dengan pesan sukses
            header("Location: supplier.php?message=deleted");
            exit;

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Jika ID tidak ditemukan
        echo "Supplier tidak ditemukan!";
    }

    $stmt->close();
} else {
    // Jika ID tidak ada di URL
    echo "ID Supplier tidak valid.";
}
?>
