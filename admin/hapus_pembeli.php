<?php
session_start();
include '../koneksi.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Cek apakah parameter id ada
if (!isset($_GET['id'])) {
    header("Location: pembeli.php?error=invalid_id");
    exit;
}

$pembeli_id = $_GET['id'];

// Mulai transaksi untuk memastikan konsistensi data
$conn->begin_transaction();

try {
    // 1. Ambil user_id dari tabel pembeli
    $sql_get_user = "SELECT user_id_222233 FROM pembeli_222233 WHERE pembeli_id_222233 = ?";
    $stmt_get_user = $conn->prepare($sql_get_user);
    $stmt_get_user->bind_param("i", $pembeli_id);
    $stmt_get_user->execute();
    $result_get_user = $stmt_get_user->get_result();
    
    if ($result_get_user->num_rows === 0) {
        throw new Exception("Data pembeli tidak ditemukan");
    }
    
    $row_user = $result_get_user->fetch_assoc();
    $user_id = $row_user['user_id_222233'];
    $stmt_get_user->close();

    // 2. Hapus dari tabel pembeli
    $sql_delete_pembeli = "DELETE FROM pembeli_222233 WHERE pembeli_id_222233 = ?";
    $stmt_delete_pembeli = $conn->prepare($sql_delete_pembeli);
    $stmt_delete_pembeli->bind_param("i", $pembeli_id);
    $stmt_delete_pembeli->execute();
    $stmt_delete_pembeli->close();

    // 3. Hapus dari tabel users
    $sql_delete_user = "DELETE FROM users_222233 WHERE user_id_222233 = ?";
    $stmt_delete_user = $conn->prepare($sql_delete_user);
    $stmt_delete_user->bind_param("i", $user_id);
    $stmt_delete_user->execute();
    $stmt_delete_user->close();

    // Commit transaksi jika semua query berhasil
    $conn->commit();
    
    header("Location: pembeli.php?message=delete_success");
    exit;
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $conn->rollback();
    
    header("Location: pembeli.php?error=delete_failed&message=" . urlencode($e->getMessage()));
    exit;
}
?>