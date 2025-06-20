<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
$message = '';
$kategori_options = ['Tablet', 'Sirup', 'Kapsul', 'Salep', 'Obat Tetes', 'Injeksi'];

if (!$id) {
    header("Location: penawaran_obat.php");
    exit();
}

// Ambil supplier_id berdasarkan user_id
$stmt = $conn->prepare("SELECT supplier_id_222233 FROM supplier_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $supplier_id = $row['supplier_id_222233'];
} else {
    die("Supplier tidak ditemukan.");
}
$stmt->close();

// Ambil data penawaran
$query = "SELECT * FROM penawaran_obat_222233 WHERE penawaran_id_222233 = ? AND supplier_id_222233 = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id, $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$penawaran = $result->fetch_assoc();
$stmt->close();

if (!$penawaran) {
    header("Location: penawaran_obat.php");
    exit();
}

if ($penawaran['status_penawaran_222233'] !== 'pending') {
    $message = "Penawaran tidak bisa diedit karena statusnya sudah " . $penawaran['status_penawaran_222233'] . ".";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $jenis_obat = mysqli_real_escape_string($conn, $_POST['jenis_obat']);
    $kategori = $_POST['kategori'];
    $jumlah = (int)$_POST['jumlah'];
    $harga_satuan = (float)$_POST['harga_satuan'];

    if ($nama_obat && $jenis_obat && in_array($kategori, $kategori_options) && $jumlah > 0 && $harga_satuan > 0) {
$update = "UPDATE penawaran_obat_222233 SET nama_obat_222233 = ?, jenis_obat_222233 = ?, kategori_222233 = ?, jumlah_222233 = ?, harga_satuan_222233 = ?, gambar_obat_222233 = ? WHERE penawaran_id_222233 = ? AND supplier_id_222233 = ?";
$stmt = $conn->prepare($update);
$stmt->bind_param("sssddssi", $nama_obat, $jenis_obat, $kategori, $jumlah, $harga_satuan, $gambar_final, $id, $supplier_id);

$gambar_baru = $_FILES['gambar']['name'] ?? '';
$upload_dir = '../uploads/';
$gambar_lama = $penawaran['gambar_obat_222233'];

if ($gambar_baru && $_FILES['gambar']['error'] === 0) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($gambar_baru, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed_ext)) {
        $nama_file_baru = uniqid('obat_', true) . '.' . $ext;
        $target_path = $upload_dir . $nama_file_baru;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
            // Hapus gambar lama jika ada
            if ($gambar_lama && file_exists($upload_dir . $gambar_lama)) {
                unlink($upload_dir . $gambar_lama);
            }
            $gambar_final = $nama_file_baru;
        } else {
            $message = "Gagal mengupload gambar baru.";
        }
    } else {
        $message = "Ekstensi file tidak didukung.";
    }
} else {
    $gambar_final = $gambar_lama; // Gunakan gambar lama jika tidak diubah
}

        if ($stmt->execute()) {
            header("Location: penawaran_obat.php?message=Penawaran berhasil diperbarui.");
            exit();
        } else {
            $message = "Gagal memperbarui penawaran: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Semua kolom harus diisi dengan benar dan kategori harus valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penawaran Obat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Penawaran Obat</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($penawaran['status_penawaran_222233'] === 'pending'): ?>
<form method="POST" action="" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        <div>
            <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
            <input type="text" name="nama_obat" id="nama_obat" required
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   value="<?php echo htmlspecialchars($penawaran['nama_obat_222233']); ?>">
        </div>

        <div>
            <label for="jenis_obat" class="block text-sm font-medium text-gray-700">Jenis Obat</label>
            <input type="text" name="jenis_obat" id="jenis_obat" required
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   value="<?php echo htmlspecialchars($penawaran['jenis_obat_222233']); ?>">
        </div>

        <div>
            <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="kategori" id="kategori" required
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                <option value="" disabled>Pilih kategori</option>
                <?php foreach ($kategori_options as $k): ?>
                    <option value="<?php echo $k; ?>" <?php if ($penawaran['kategori_222233'] === $k) echo 'selected'; ?>><?php echo $k; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" required min="1"
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   value="<?php echo htmlspecialchars($penawaran['jumlah_222233']); ?>">
        </div>

        <div>
            <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
            <input type="number" name="harga_satuan" id="harga_satuan" required min="0" step="0.01"
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   value="<?php echo htmlspecialchars($penawaran['harga_satuan_222233']); ?>">
        </div>
        <div>
    <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Obat</label>
    <?php if (!empty($penawaran['gambar_obat_222233'])): ?>
        <div class="mb-2">
            <img src="../uploads/<?php echo htmlspecialchars($penawaran['gambar_obat_222233']); ?>"
                 alt="Gambar Obat" class="h-24 object-contain border rounded">
        </div>
    <?php endif; ?>
    <input type="file" name="gambar" id="gambar"
           class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
    <p class="text-sm text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti gambar.</p>
</div>


        <div class="flex justify-end space-x-4">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                Simpan Perubahan
            </button>
            <a href="penawaran_obat.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
    <?php endif; ?>
</div>

</body>
</html>