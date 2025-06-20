<?php
session_start();
include 'koneksi.php';

if (isset($_POST['id']) && isset($_POST['jumlah'])) {
    $keranjang_id = intval($_POST['id']);
    $jumlah_baru = intval($_POST['jumlah']);
    $user_id = $_SESSION['user_id'];

    // Pastikan item milik user yang sedang login (keamanan)
    $query = "UPDATE keranjang_222233 
              SET jumlah_222233 = ? 
              WHERE keranjang_id_222233 = ? AND user_id_222233 = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $jumlah_baru, $keranjang_id, $user_id);

    if ($stmt->execute()) {
        echo "sukses";
    } else {
        http_response_code(500);
        echo "gagal";
    }
}
?>
