<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat transaksi berdasarkan user
$query = "
    SELECT t.*, p.metode_pembayaran_222233, p.status_pembayaran_222233, p.tanggal_pembayaran_222233, p.jumlah_pembayaran_222233
    FROM transaksi_222233 t
    LEFT JOIN pembayaran_222233 p ON t.transaksi_id_222233 = p.transaksi_id_222233
    WHERE t.user_id_222233 = ?
    GROUP BY t.transaksi_id_222233
    ORDER BY t.tanggal_transaksi_222233 DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transaksi_data = $result->fetch_all(MYSQLI_ASSOC);
// Tambah detail obat untuk setiap transaksi
foreach ($transaksi_data as &$transaksi) {
    $transaksi_id = $transaksi['transaksi_id_222233'];
    $query_obat = "
        SELECT o.nama_obat_222233, k.jumlah_222233
        FROM keranjang_222233 k
        JOIN obat_222233 o ON k.obat_id_222233 = o.obat_id_222233
        WHERE k.transaksi_id_222233 = ?
    ";
    $stmt_obat = $conn->prepare($query_obat);
    $stmt_obat->bind_param("i", $transaksi_id);
    $stmt_obat->execute();
    $result_obat = $stmt_obat->get_result();
    $transaksi['obat_list'] = $result_obat->fetch_all(MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Riwayat Transaksi & Pembayaran</h1>

    <?php if (empty($transaksi_data)): ?>
        <div class="bg-yellow-100 p-4 rounded-lg text-yellow-800 shadow">
            Anda belum memiliki riwayat transaksi.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-teal-600 text-white">
    <tr>
        <th class="p-4">Tanggal Transaksi</th>
        <th class="p-4">Obat</th> <!-- Tambahan -->
        <th class="p-4">Total Harga</th>
        <th class="p-4">Status Transaksi</th>
        <th class="p-4">Metode Pembayaran</th>
        <th class="p-4">Status Pembayaran</th>
        <th class="p-4">Tanggal Pembayaran</th>
        <th class="p-4">Jumlah Pembayaran</th>
    </tr>
</thead>
<tbody class="text-gray-800">
    <?php foreach ($transaksi_data as $row): ?>
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="p-4"><?= date('d-m-Y H:i', strtotime($row['tanggal_transaksi_222233'])) ?></td>

            <!-- TAMPILKAN NAMA OBAT -->
<td class="p-4">
    <?php foreach ($row['obat_list'] as $obat): ?>
        <?= htmlspecialchars($obat['nama_obat_222233']) ?> (x<?= $obat['jumlah_222233'] ?>)<br>
    <?php endforeach; ?>
</td>


            <td class="p-4">Rp <?= number_format($row['total_harga_222233'], 2, ',', '.') ?></td>
            <td class="p-4">
                <span class="px-2 py-1 rounded text-white text-xs font-semibold
                    <?= $row['status_transaksi_222233'] === 'sukses' ? 'bg-green-600' : ($row['status_transaksi_222233'] === 'pending' ? 'bg-yellow-600' : 'bg-red-600') ?>">
                    <?= ucfirst($row['status_transaksi_222233']) ?>
                </span>
            </td>
            <td class="p-4"><?= ucfirst($row['metode_pembayaran_222233'] ?? '-') ?></td>
            <td class="p-4">
                <span class="px-2 py-1 rounded text-white text-xs font-semibold
                    <?= $row['status_pembayaran_222233'] === 'sudah_dibayar' ? 'bg-green-600' : ($row['status_pembayaran_222233'] === 'belum_dibayar' ? 'bg-yellow-600' : 'bg-red-600') ?>">
                    <?= ucfirst($row['status_pembayaran_222233'] ?? '-') ?>
                </span>
            </td>
            <td class="p-4"><?= $row['tanggal_pembayaran_222233'] ? date('d-m-Y H:i', strtotime($row['tanggal_pembayaran_222233'])) : '-' ?></td>
            <td class="p-4">Rp <?= number_format($row['jumlah_pembayaran_222233'] ?? 0, 2, ',', '.') ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>

            </table>
        </div>
    <?php endif; ?>
</div>

</body>

</html>
