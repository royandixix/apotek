<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../login.php");
    exit();
}

// ✅ CEK DULU apakah ada data keranjang yang dipilih
if (!isset($_SESSION['keranjang_terpilih']) || empty($_SESSION['keranjang_terpilih'])) {
    header("Location: keranjang.php?error=pilih");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ✅ BARU PROSES SESSION SETELAH DIPASTIKAN TERSEDIA
$keranjang_ids = array_map('intval', $_SESSION['keranjang_terpilih']);
$ids = implode(',', $keranjang_ids);

$query = "SELECT k.*, o.nama_obat_222233 
          FROM keranjang_222233 k 
          JOIN obat_222233 o ON k.obat_id_222233 = o.obat_id_222233 
          WHERE k.keranjang_id_222233 IN ($ids) 
            AND k.user_id_222233 = ? 
            AND k.transaksi_id_222233 IS NULL
            AND k.is_deleted_222233 = 0";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$keranjang_items = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total
$total = 0;
foreach ($keranjang_items as $item) {
    $total += $item['harga_222233'] * $item['jumlah_222233'];
}

// Proses pembayaran saat form dikirim
if (isset($_POST['bayar']) && !empty($keranjang_items)) {
    $metode = $_POST['metode'];

    // Tambahkan transaksi
    $insert_transaksi = "INSERT INTO transaksi_222233 (kasir_id_222233, user_id_222233, tanggal_transaksi_222233, status_transaksi_222233, total_harga_222233)
                         VALUES (NULL, ?, NOW(), 'pending', ?)";
    $stmt_trans = $conn->prepare($insert_transaksi);
    $stmt_trans->bind_param("id", $user_id, $total);
    $stmt_trans->execute();
    $transaksi_id = $conn->insert_id;

    // Update keranjang
    $update_keranjang = "UPDATE keranjang_222233 SET transaksi_id_222233 = ? WHERE keranjang_id_222233 IN ($ids)";
    $stmt_update = $conn->prepare($update_keranjang);
    $stmt_update->bind_param("i", $transaksi_id);
    $stmt_update->execute();

    // Tambah ke pembayaran dan update stok
    foreach ($keranjang_items as $item) {
        $jumlah_bayar = $item['harga_222233'] * $item['jumlah_222233'];

        $insert_bayar = "INSERT INTO pembayaran_222233 (keranjang_id_222233, transaksi_id_222233, metode_pembayaran_222233, status_pembayaran_222233, tanggal_pembayaran_222233, jumlah_pembayaran_222233)
                         VALUES (?, ?, ?, 'belum_dibayar', NOW(), ?)";
        $stmt_bayar = $conn->prepare($insert_bayar);
        $stmt_bayar->bind_param("iisd", $item['keranjang_id_222233'], $transaksi_id, $metode, $jumlah_bayar);
        $stmt_bayar->execute();

        $kurangi_stok = "UPDATE obat_222233 SET stok_222233 = stok_222233 - ? WHERE obat_id_222233 = ?";
        $stmt_stok = $conn->prepare($kurangi_stok);
        $stmt_stok->bind_param("ii", $item['jumlah_222233'], $item['obat_id_222233']);
        $stmt_stok->execute();
    }

    unset($_SESSION['keranjang_terpilih']);
    $_SESSION['success_message'] = "Pembayaran berhasil diproses. Menunggu konfirmasi kasir.";
    header("Location: riwayat_transaksi.php");
    exit();
}
?>


<!-- Tampilan form pembayaran -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Pembayaran</h1>

    <?php if (!empty($keranjang_items)): ?>
        <!-- TABEL KERANJANG -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-md mb-6">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="p-4">Nama Obat</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Jumlah</th>
                        <th class="p-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php foreach ($keranjang_items as $item): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4"><?= htmlspecialchars($item['nama_obat_222233']) ?></td>
                            <td class="p-4">Rp <?= number_format($item['harga_222233'], 2, ',', '.') ?></td>
                            <td class="p-4"><?= $item['jumlah_222233'] ?></td>
                            <td class="p-4">Rp <?= number_format($item['harga_222233'] * $item['jumlah_222233'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="3" class="p-4 text-right">Total</td>
                        <td class="p-4 text-teal-700">Rp <?= number_format($total, 2, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- FORM PEMBAYARAN -->
        <div class="bg-white rounded-xl shadow-md p-6 max-w-md">
            <form method="POST" class="space-y-4">
                <div>
                    <label for="metode" class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="metode" id="metode" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="transfer">Transfer</option>
                        <option value="cod">COD</option>
                        <option value="qris">QRIS</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>
                <button type="submit" name="bayar" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-lg shadow transition">
                    Bayar Sekarang
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded-lg shadow">
            Tidak ada item dalam keranjang untuk diproses.
        </div>
    <?php endif; ?>
</div>

</body>

</html>
