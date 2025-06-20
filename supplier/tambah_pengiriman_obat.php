<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$supplier_id = null;

// Ambil supplier_id
$stmt = $conn->prepare("SELECT supplier_id_222233 FROM supplier_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $supplier_id = $row['supplier_id_222233'];
}
$stmt->close();

// Ambil daftar penawaran yang diterima dan belum dikirim
$penawaran_list = [];
$sql = "SELECT * FROM penawaran_obat_222233 
        WHERE status_penawaran_222233 = 'diterima' 
        AND supplier_id_222233 = ? 
        AND penawaran_id_222233 NOT IN (
            SELECT penawaran_id_222233 FROM pengiriman_obat_222233
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$penawaran_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $penawaran_id = (int)$_POST['penawaran_id'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_pengiriman = $_POST['tanggal_pengiriman'];
    $status_pengiriman = $_POST['status_pengiriman'];

    // Ambil data penawaran yang akan dikirim
    $stmt = $conn->prepare("SELECT * FROM penawaran_obat_222233 WHERE penawaran_id_222233 = ? AND supplier_id_222233 = ?");
    $stmt->bind_param("ii", $penawaran_id, $supplier_id);
    $stmt->execute();
    $penawaran = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$penawaran) {
        $message = "Penawaran tidak valid.";
    } else {
$nama_obat = $penawaran['nama_obat_222233'];
$jenis = $penawaran['jenis_obat_222233'];
$kategori = $penawaran['kategori_222233'];
$harga_satuan = $penawaran['harga_satuan_222233'];
$gambar = $penawaran['gambar_obat_222233'];

// Cek apakah obat sudah ada berdasarkan nama, jenis, dan kategori
$stmt = $conn->prepare("SELECT * FROM obat_222233 WHERE nama_obat_222233 = ? AND jenis_obat_222233 = ? AND kategori_222233 = ?");
$stmt->bind_param("sss", $nama_obat, $jenis, $kategori);
$stmt->execute();
$obat = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($obat) {
    // Update stok dan harga jika sudah ada
    $new_stok = $obat['stok_222233'] + $jumlah;
    $stmt = $conn->prepare("UPDATE obat_222233 SET stok_222233 = ?, harga_222233 = ? WHERE obat_id_222233 = ?");
    $stmt->bind_param("idi", $new_stok, $harga_satuan, $obat['obat_id_222233']);
    $stmt->execute();
    $obat_id = $obat['obat_id_222233'];
    $stmt->close();
} else {
    // Insert obat baru
    $tanggal_kadaluarsa = date('Y-m-d', strtotime('+1 year')); // default 1 tahun dari sekarang
    $gambar = $penawaran['gambar_obat_222233']; // Ambil gambar dari penawaran

    $stmt = $conn->prepare("INSERT INTO obat_222233 
        (nama_obat_222233, jenis_obat_222233, stok_222233, harga_222233, tanggal_kadaluarsa_222233, kategori_222233, gambar_obat_222233) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $nama_obat, $jenis, $jumlah, $harga_satuan, $tanggal_kadaluarsa, $kategori, $gambar);
    $stmt->execute();
    $obat_id = $stmt->insert_id;
    $stmt->close();
}



$stmt = $conn->prepare("INSERT INTO pengiriman_obat_222233 
    (obat_id_222233, jumlah_222233, tanggal_pengiriman_222233, status_pengiriman_222233, supplier_id_222233, penawaran_id_222233, gambar_obat_222233) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iississ", $obat_id, $jumlah, $tanggal_pengiriman, $status_pengiriman, $supplier_id, $penawaran_id, $gambar);

        if ($stmt->execute()) {
            header("Location: pengiriman_obat.php?message=Pengiriman berhasil ditambahkan.");
            exit();
        } else {
            $message = "Gagal menyimpan pengiriman: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengiriman Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Pengiriman Obat</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($penawaran_list)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            <p>Tidak ada penawaran yang diterima atau semua penawaran sudah diproses.</p>
            <a href="penawaran_obat.php" class="text-teal-600 hover:text-teal-800 font-medium">Lihat Penawaran Obat</a>
        </div>
    <?php else: ?>
        <form method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            <div>
                <label for="penawaran_id" class="block text-sm font-medium text-gray-700">Pilih Obat (Penawaran Diterima)</label>
                <select name="penawaran_id" id="penawaran_id" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                    <option value="">-- Pilih Obat --</option>
                    <?php foreach ($penawaran_list as $p): ?>
                        <option value="<?= $p['penawaran_id_222233']; ?>" 
                                data-jumlah="<?= $p['jumlah_222233']; ?>"
                                data-harga="<?= $p['harga_satuan_222233']; ?>">
                            <?= htmlspecialchars($p['nama_obat_222233']); ?> 
                            (Jumlah: <?= $p['jumlah_222233']; ?>, Harga: Rp<?= number_format($p['harga_satuan_222233'], 0, ',', '.'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" required
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg" readonly>
            </div>

            <div>
                <label for="tanggal_pengiriman" class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
                <input type="date" name="tanggal_pengiriman" id="tanggal_pengiriman" required
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                       min="<?= date('Y-m-d'); ?>">
            </div>

            <div>
                <label for="status_pengiriman" class="block text-sm font-medium text-gray-700">Status Pengiriman</label>
                <select name="status_pengiriman" id="status_pengiriman" required
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                    <option value="diproses">Diproses</option>
                    <!-- <option value="dikirim">Dikirim</option> -->
                    <!-- <option value="diterima">Diterima</option> -->
                </select>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                    Simpan
                </button>
                <a href="pengiriman_obat.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                    Batal
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>
<script>
    document.getElementById('penawaran_id').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const jumlah = selected.getAttribute('data-jumlah');
        document.getElementById('jumlah').value = jumlah || '';
    });
</script>

</body>
</html>