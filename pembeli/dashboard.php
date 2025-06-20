<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Penanganan untuk menambahkan ke keranjang
if (isset($_POST['add_to_cart'])) {
    $obat_id = (int)$_POST['obat_id'];
    $jumlah = (int)$_POST['jumlah'];

    // Ambil harga saat ini dari tabel obat
    $query_harga = "SELECT harga_222233 FROM obat_222233 WHERE obat_id_222233 = ?";
    $stmt_harga = $conn->prepare($query_harga);
    $stmt_harga->bind_param("i", $obat_id);
    $stmt_harga->execute();
    $result_harga = $stmt_harga->get_result();

    if ($result_harga->num_rows > 0) {
        $row = $result_harga->fetch_assoc();
        $harga = $row['harga_222233'];

        // Cek apakah obat sudah ada di keranjang user dan belum masuk transaksi
        $cek_query = "SELECT keranjang_id_222233, jumlah_222233 FROM keranjang_222233 
                      WHERE user_id_222233 = ? AND obat_id_222233 = ? AND transaksi_id_222233 IS NULL";
        $stmt_cek = $conn->prepare($cek_query);
        $stmt_cek->bind_param("ii", $user_id, $obat_id);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0) {
            // Obat sudah ada di keranjang → update jumlah
            $row_cek = $result_cek->fetch_assoc();
            $keranjang_id = $row_cek['keranjang_id_222233'];
            $jumlah_baru = $row_cek['jumlah_222233'] + $jumlah;

            $update_query = "UPDATE keranjang_222233 SET jumlah_222233 = ? WHERE keranjang_id_222233 = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("ii", $jumlah_baru, $keranjang_id);
            $stmt_update->execute();

            $message = "Jumlah obat di keranjang diperbarui.";
        } else {
            // Obat belum ada → insert baru
            $insert_query = "INSERT INTO keranjang_222233 (user_id_222233, transaksi_id_222233, obat_id_222233, harga_222233, jumlah_222233)
                             VALUES (?, NULL, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_query);
            $stmt_insert->bind_param("iidi", $user_id, $obat_id, $harga, $jumlah);
            $stmt_insert->execute();

            $message = "Obat berhasil ditambahkan ke keranjang.";
        }
    } else {
        $message = "Obat tidak ditemukan.";
    }
}

// Ambil semua data obat
$obat_query = "SELECT * FROM obat_222233";
$obat_result = mysqli_query($conn, $obat_query);
$obat_data = mysqli_fetch_all($obat_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pembeli</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700 mb-6">Dashboard Pembeli</h1>
        <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! Anda login sebagai Pembeli.</p>

        <?php if (!empty($message)): ?>
            <div class="mt-4 p-4 bg-blue-100 text-blue-800 rounded-lg shadow"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2 class="text-2xl font-semibold text-teal-700 mt-8 mb-4">Daftar Obat Tersedia</h2>
        <div class="obat-list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if (empty($obat_data)): ?>
                <p class="text-gray-500 col-span-full">Belum ada obat tersedia saat ini.</p>
            <?php else: ?>
                <?php foreach ($obat_data as $obat): ?>
<div class="obat-card bg-white border border-gray-300 rounded-lg p-6 shadow-md h-full flex flex-col justify-between">
    <!-- Gambar Obat -->
    <?php if (!empty($obat['gambar_obat_222233'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($obat['gambar_obat_222233']); ?>"
             alt="Gambar <?php echo htmlspecialchars($obat['nama_obat_222233']); ?>"
             class="w-full h-40 object-cover rounded-md mb-4 border border-teal-100">
    <?php else: ?>
        <div class="w-full h-40 bg-gray-100 rounded-md mb-4 flex items-center justify-center text-gray-400 text-sm border border-dashed border-gray-300">
            Tidak ada gambar
        </div>
    <?php endif; ?>

    <!-- Informasi Obat -->
    <div>
        <h3 class="text-xl font-semibold text-teal-700">
            <?php echo htmlspecialchars($obat['nama_obat_222233']); ?>
        </h3>
        <p><strong>Jenis:</strong> <?php echo htmlspecialchars($obat['jenis_obat_222233']); ?></p>
        <p><strong>Kategori:</strong> <?php echo htmlspecialchars($obat['kategori_222233']); ?></p>
        <p><strong>Stok:</strong> <?php echo htmlspecialchars($obat['stok_222233']); ?></p>
        <p class="text-lg font-semibold text-teal-600 mt-2">
            Harga: Rp <?php echo number_format($obat['harga_222233'], 2, ',', '.'); ?>
        </p>
    </div>

    <!-- Form Tambah ke Keranjang -->
    <div class="mt-4">
        <?php if ($obat['stok_222233'] > 0): ?>
            <form action="dashboard.php" method="POST">
                <input type="hidden" name="obat_id" value="<?php echo htmlspecialchars($obat['obat_id_222233']); ?>">
                <div class="mb-2">
                    <label for="jumlah_<?php echo $obat['obat_id_222233']; ?>" class="block text-sm font-medium text-gray-700">Jumlah:</label>
                    <input type="number" id="jumlah_<?php echo $obat['obat_id_222233']; ?>" name="jumlah" value="1" min="1" max="<?php echo htmlspecialchars($obat['stok_222233']); ?>" required class="block w-20 p-2 border border-gray-300 rounded-md">
                </div>
                <button type="submit" name="add_to_cart" class="w-full bg-teal-600 text-white px-4 py-2 rounded-lg shadow hover:bg-teal-700 transition">Tambah ke Keranjang</button>
            </form>
        <?php else: ?>
            <p class="text-red-500 font-bold">Stok Habis</p>
        <?php endif; ?>
    </div>
</div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
