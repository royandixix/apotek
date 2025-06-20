<?php
session_start();
include '../koneksi.php';

// Cek apakah user adalah kasir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil kasir_id
$kasir_id = null;
$stmt = $conn->prepare("SELECT kasir_id_222233 FROM kasir_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $kasir_id = $row['kasir_id_222233'];
}
$stmt->close();

// Proses simpan transaksi dari keranjang
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pembeli_id'])) {
    $pembeli_id = $_POST['pembeli_id'];

    // Ambil item dari keranjang user ini
    $keranjang = [];
    $total_harga = 0;

    // Ambil isi keranjang untuk user yang sedang login
    $query = $conn->prepare("SELECT k.*, o.nama_obat_222233, o.harga_222233 FROM keranjang_222233 k JOIN obat_222233 o ON k.obat_id_222233 = o.obat_id_222233 WHERE k.user_id_222233 = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    while ($item = $result->fetch_assoc()) {
        $subtotal = $item['harga_222233'] * $item['jumlah_222233'];
        $keranjang[] = $item;
        $total_harga += $subtotal;
    }
    $query->close();

    if ($kasir_id && $total_harga > 0 && count($keranjang) > 0) {
        // Simpan transaksi
        $stmt = $conn->prepare("INSERT INTO transaksi_222233 (kasir_id_222233, pembeli_id_222233, total_harga_222233, tanggal_transaksi_222233, status_transaksi_222233) VALUES (?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("iid", $kasir_id, $pembeli_id, $total_harga);
        $stmt->execute();
        $transaksi_id = $stmt->insert_id;  // Ambil ID transaksi yang baru disimpan
        $stmt->close();

        // Update stok obat dan simpan detail transaksi
        foreach ($keranjang as $item) {
            $obat_id = $item['obat_id_222233'];
            $jumlah = $item['jumlah_222233'];
            $harga = $item['harga_222233'];

            // Update stok obat
            $update = $conn->prepare("UPDATE obat_222233 SET stok_222233 = stok_222233 - ? WHERE obat_id_222233 = ?");
            $update->bind_param("ii", $jumlah, $obat_id);
            $update->execute();
            $update->close();

            // Simpan detail transaksi
            $insert_detail = $conn->prepare("INSERT INTO detail_transaksi_222233 (transaksi_id_222233, obat_id_222233, jumlah_222233, harga_saat_itu_222233) VALUES (?, ?, ?, ?)");
            $insert_detail->bind_param("iiid", $transaksi_id, $obat_id, $jumlah, $harga);
            $insert_detail->execute();
            $insert_detail->close();
        }

        // Hapus isi keranjang user
        $conn->query("DELETE FROM keranjang_222233 WHERE user_id_222233 = $user_id");

        $pesan = '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">Transaksi berhasil disimpan dan keranjang dikosongkan.</div>';
    } else {
        $pesan = '<div class="bg-red-100 text-red-800 p-4 rounded mb-4">Gagal menyimpan transaksi. Keranjang kosong atau data tidak valid.</div>';
    }
}

// Ambil pembeli
$pembeli_result = $conn->query("SELECT p.pembeli_id_222233, u.nama_222233 FROM pembeli_222233 p JOIN users_222233 u ON p.user_id_222233 = u.user_id_222233");

// Ambil isi keranjang user
$keranjang_result = $conn->prepare("SELECT k.*, o.nama_obat_222233, o.harga_222233 FROM keranjang_222233 k JOIN obat_222233 o ON k.obat_id_222233 = o.obat_id_222233 WHERE k.user_id_222233 = ?");
$keranjang_result->bind_param("i", $user_id);
$keranjang_result->execute();
$keranjang_items = $keranjang_result->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pembelian</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Pembelian</h1>

    <?php echo $pesan; ?>

    <form action="" method="POST" class="bg-white p-6 rounded-xl shadow space-y-6">
        <!-- Pilih Pembeli -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Pilih Pembeli</label>
            <select name="pembeli_id" required class="w-full border border-gray-300 rounded px-4 py-2">
                <option value="">-- Pilih Pembeli --</option>
                <?php while ($pembeli = $pembeli_result->fetch_assoc()): ?>
                    <option value="<?php echo $pembeli['pembeli_id_222233']; ?>">
                        <?php echo htmlspecialchars($pembeli['nama_222233']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Tampilkan Keranjang -->
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Produk di Keranjang</label>
            <div class="space-y-2 max-h-72 overflow-y-auto">
                <?php if ($keranjang_items->num_rows > 0): ?>
                    <?php while ($item = $keranjang_items->fetch_assoc()): ?>
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-semibold"><?php echo $item['nama_obat_222233']; ?></p>
                                <p class="text-sm text-gray-600">
                                    Rp <?php echo number_format($item['harga_222233'], 0, ',', '.'); ?> 
                                    | Qty: <?php echo $item['jumlah_222233']; ?> 
                                    | Stok Saat Ini: <?php echo $item['stok_222233']; ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">Keranjang kosong.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                Simpan
            </button>
            <a href="transaksi_pembelian.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
</div>

</body>
</html>