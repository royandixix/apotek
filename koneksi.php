<?php
// Konfigurasi database
$host = 'localhost'; // Ganti dengan host database Anda
$user = 'root';      // Ganti dengan username database Anda
$pass = '';          // Ganti dengan password database Anda
$db_name = 'sirri_222233'; // Nama database

// Buat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db_name);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 untuk mendukung emoji dan karakter khusus
$conn->set_charset("utf8mb4");
?>