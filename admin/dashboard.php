<?php
session_start();
include '../koneksi.php';

// Cek apakah user sudah login dan punya role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700">Dashboard Admin</h1>
        <p class="text-gray-700 mt-2">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Anda login sebagai <span class="font-semibold">Administrator</span>.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
    <!-- Total Obat -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-400">
        <h3 class="text-lg font-semibold text-teal-700 mb-2">Total Obat</h3>
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_obat FROM obat_222233");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-3xl font-bold text-gray-800'>" . $data['total_obat'] . "</p>";
        $stmt->close();
        ?>
        <a href="obat.php" class="inline-block mt-4 text-sm text-teal-600 hover:underline">Lihat Detail</a>
    </div>

    <!-- Total Supplier -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-400">
        <h3 class="text-lg font-semibold text-green-700 mb-2">Total Supplier</h3>
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_supplier FROM supplier_222233");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-3xl font-bold text-gray-800'>" . $data['total_supplier'] . "</p>";
        $stmt->close();
        ?>
        <a href="supplier.php" class="inline-block mt-4 text-sm text-green-600 hover:underline">Lihat Detail</a>
    </div>

    <!-- Total Pembeli -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-400">
        <h3 class="text-lg font-semibold text-yellow-700 mb-2">Total Pembeli</h3>
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_pembeli FROM pembeli_222233");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-3xl font-bold text-gray-800'>" . $data['total_pembeli'] . "</p>";
        $stmt->close();
        ?>
        <a href="pembeli.php" class="inline-block mt-4 text-sm text-yellow-600 hover:underline">Lihat Detail</a>
    </div>

    <!-- Total Transaksi & Pembayaran -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
        <h3 class="text-lg font-semibold text-blue-700 mb-2">Total Transaksi & Pembayaran</h3>
        <?php
        $stmt = $conn->prepare("SELECT 
                                    (SELECT COUNT(*) FROM transaksi_222233) AS total_transaksi,
                                    (SELECT COUNT(*) FROM pembayaran_222233) AS total_pembayaran");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-lg text-gray-700'>Transaksi: <span class='text-2xl font-bold'>" . $data['total_transaksi'] . "</span></p>";
        echo "<p class='text-lg text-gray-700 mt-2'>Pembayaran: <span class='text-2xl font-bold'>" . $data['total_pembayaran'] . "</span></p>";
        $stmt->close();
        ?>
        <a href="manajemen_transaksi.php" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Lihat Detail</a>
    </div>

    <!-- Total Penawaran -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-400">
        <h3 class="text-lg font-semibold text-purple-700 mb-2">Total Penawaran Obat</h3>
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_penawaran FROM penawaran_obat_222233");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-3xl font-bold text-gray-800'>" . $data['total_penawaran'] . "</p>";
        $stmt->close();
        ?>
        <a href="penawaran.php" class="inline-block mt-4 text-sm text-purple-600 hover:underline">Lihat Detail</a>
    </div>

    <!-- Total Pengiriman -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-400">
        <h3 class="text-lg font-semibold text-indigo-700 mb-2">Total Pengiriman Obat</h3>
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) AS total_pengiriman FROM pengiriman_obat_222233");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo "<p class='text-3xl font-bold text-gray-800'>" . $data['total_pengiriman'] . "</p>";
        $stmt->close();
        ?>
        <a href="pengiriman.php" class="inline-block mt-4 text-sm text-indigo-600 hover:underline">Lihat Detail</a>
    </div>
</div>

<div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Riwayat Transaksi Terbaru</h2>

    <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-teal-600 text-white">
                <tr>
                    <th class="p-4">ID Transaksi</th>
                    <th class="p-4">Kasir</th>
                    <th class="p-4">Pembeli</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Total</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <?php
                $sql = "
                    SELECT 
                        t.transaksi_id_222233,
                        t.tanggal_transaksi_222233,
                        t.status_transaksi_222233,
                        t.total_harga_222233,
                        uk.nama_222233 AS nama_kasir,
                        up.nama_222233 AS nama_pembeli
                    FROM transaksi_222233 t
                    LEFT JOIN kasir_222233 k ON t.kasir_id_222233 = k.kasir_id_222233
                    LEFT JOIN users_222233 uk ON k.user_id_222233 = uk.user_id_222233
                    LEFT JOIN users_222233 up ON t.user_id_222233 = up.user_id_222233
                    ORDER BY t.tanggal_transaksi_222233 DESC
                    LIMIT 10
                ";

                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4"><?php echo htmlspecialchars($row['transaksi_id_222233']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['nama_kasir']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['nama_pembeli']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars(date("d-m-Y H:i", strtotime($row['tanggal_transaksi_222233']))); ?></td>
                            <td class="p-4">
                                <?php
                                    $status = $row['status_transaksi_222233'];
                                    $badge = match($status) {
                                        'sukses' => 'bg-green-200 text-green-800',
                                        'pending' => 'bg-yellow-200 text-yellow-800',
                                        'batal' => 'bg-red-200 text-red-800',
                                        default => 'bg-gray-200 text-gray-800',
                                    };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $badge; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td class="p-4">Rp <?php echo number_format($row['total_harga_222233'], 0, ',', '.'); ?></td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">Belum ada transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
</body>
</html>
