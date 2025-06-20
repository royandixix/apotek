<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil supplier_id berdasarkan user_id
$stmt = $conn->prepare("SELECT supplier_id_222233 FROM supplier_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
$supplier_id = $supplier['supplier_id_222233'];
$stmt->close();

// Hitung total penawaran
$stmt = $conn->prepare("SELECT COUNT(*) AS total_penawaran FROM penawaran_obat_222233 WHERE supplier_id_222233 = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_penawaran = $row['total_penawaran'];
$stmt->close();

// Hitung total pengiriman
$stmt = $conn->prepare("SELECT COUNT(*) AS total_pengiriman FROM pengiriman_obat_222233 WHERE supplier_id_222233 = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_pengiriman = $row['total_pengiriman'];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-4">Dashboard Supplier</h1>
    <p class="text-gray-700 mb-6">Selamat datang, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>! Anda login sebagai <span class="italic">Supplier</span>.</p>

    <div class="flex flex-wrap gap-6">
        <!-- Kartu Total Penawaran -->
        <div class="bg-teal-50 p-6 rounded-xl shadow flex-1 min-w-[250px]">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Penawaran</h3>
            <p class="text-3xl font-bold text-teal-700"><?php echo $total_penawaran; ?></p>
            <a href="penawaran_obat.php" class="inline-block mt-4 bg-teal-600 text-white px-4 py-2 rounded shadow hover:bg-teal-700 transition">Lihat Detail</a>
        </div>

        <!-- Kartu Total Pengiriman -->
        <div class="bg-green-50 p-6 rounded-xl shadow flex-1 min-w-[250px]">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Pengiriman</h3>
            <p class="text-3xl font-bold text-green-700"><?php echo $total_pengiriman; ?></p>
            <a href="pengiriman_obat.php" class="inline-block mt-4 bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">Lihat Detail</a>
        </div>
    </div>
</div>

</body>
</html>
