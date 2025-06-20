<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Transaksi & Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Manajemen Transaksi & Pembayaran</h1>

    <!-- Tombol Cetak -->
    <div class="mb-4">
        <form action="cetak_transaksi.php" method="GET" target="_blank" class="flex gap-2">
            <select name="tipe" class="border border-gray-300 rounded px-3 py-2 text-sm text-gray-700">
                <option value="struk">Struk</option>
                <option value="laporan">Laporan</option>
            </select>
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
                Cetak
            </button>
        </form>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
        <table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th>
            <th class="p-4">ID Transaksi</th>
            <th class="p-4">Tanggal</th>
            <th class="p-4">Total Harga</th>
            <th class="p-4">Status Transaksi</th>
            <th class="p-4">Metode Pembayaran</th>
            <th class="p-4">Status Pembayaran</th>
            <th class="p-4">Tanggal Pembayaran</th>
            <th class="p-4">Jumlah Dibayar</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php
        $query = "SELECT 
                    t.transaksi_id_222233, 
                    t.tanggal_transaksi_222233,
                    t.total_harga_222233,
                    t.status_transaksi_222233,
                    p.metode_pembayaran_222233,
                    p.status_pembayaran_222233,
                    p.tanggal_pembayaran_222233,
                    p.jumlah_pembayaran_222233
                  FROM transaksi_222233 t
                  LEFT JOIN pembayaran_222233 p ON t.transaksi_id_222233 = p.transaksi_id_222233
                  ORDER BY t.tanggal_transaksi_222233 DESC";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='border-b hover:bg-gray-50 transition'>";
                echo "<td class='p-4'>" . $no++ . "</td>";
                echo "<td class='p-4'>{$row['transaksi_id_222233']}</td>";
                echo "<td class='p-4'>" . date('d-m-Y H:i', strtotime($row['tanggal_transaksi_222233'])) . "</td>";
                echo "<td class='p-4'>Rp " . number_format($row['total_harga_222233'], 0, ',', '.') . "</td>";
                echo "<td class='p-4 capitalize'>{$row['status_transaksi_222233']}</td>";
                echo "<td class='p-4 capitalize'>" . ($row['metode_pembayaran_222233'] ?? '-') . "</td>";
                echo "<td class='p-4 capitalize'>" . ($row['status_pembayaran_222233'] ?? '-') . "</td>";
                echo "<td class='p-4'>" . ($row['tanggal_pembayaran_222233'] ? date('d-m-Y H:i', strtotime($row['tanggal_pembayaran_222233'])) : '-') . "</td>";
                echo "<td class='p-4'>Rp " . number_format($row['jumlah_pembayaran_222233'], 0, ',', '.') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9' class='text-center py-6 text-gray-500'>Belum ada data transaksi.</td></tr>";
        }

        $result->free();
        ?>
    </tbody>
</table>

    </div>
</div>

</body>
</html>
