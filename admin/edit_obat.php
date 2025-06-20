<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil ID obat dari parameter URL
if (!isset($_GET['id'])) {
    header("Location: obat_data.php");
    exit;
}

$obat_id = $_GET['id'];

// Ambil data obat berdasarkan ID
$sql = "SELECT * FROM obat_222233 WHERE obat_id_222233 = '$obat_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Jika data obat tidak ditemukan
    header("Location: obat_data.php");
    exit;
}

$obat = $result->fetch_assoc();
// Ambil data gambar lama
$gambar_lama = $obat['gambar_obat_222233'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = $_POST['nama_obat'];
    $jenis_obat = $_POST['jenis_obat'];
    
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $tanggal_kadaluarsa = $_POST['tanggal_kadaluarsa'];
    $kategori = $_POST['kategori'];

    // Validasi input utama
    if (empty($nama_obat) || empty($jenis_obat) || empty($stok) || empty($harga) || empty($tanggal_kadaluarsa) || empty($kategori)) {
        $error_message = "Semua field harus diisi!";
    } else {
        $nama_gambar_baru = $gambar_lama; // default jika tidak ada upload baru

        // Jika ada upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $gambar_tmp = $_FILES['gambar']['tmp_name'];
            $gambar_ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $nama_gambar_baru = 'obat_' . uniqid() . '.' . $gambar_ext;
            $target_path = '../uploads/' . $nama_gambar_baru;

            if (move_uploaded_file($gambar_tmp, $target_path)) {
                // Hapus gambar lama jika ada dan berbeda
                if (!empty($gambar_lama) && file_exists('../uploads/' . $gambar_lama)) {
                    unlink('../uploads/' . $gambar_lama);
                }
            } else {
                $error_message = "Gagal mengunggah gambar baru.";
            }
        }

        // Lanjutkan update jika tidak ada error upload
        if (!isset($error_message)) {
            $sql_update = "UPDATE obat_222233 SET 
                           nama_obat_222233 = '$nama_obat', 
                           jenis_obat_222233 = '$jenis_obat', 
                           stok_222233 = '$stok', 
                           harga_222233 = '$harga', 
                           tanggal_kadaluarsa_222233 = '$tanggal_kadaluarsa', 
                           kategori_222233 = '$kategori',
                           gambar_obat_222233 = '$nama_gambar_baru'
                           WHERE obat_id_222233 = '$obat_id'";

            if ($conn->query($sql_update) === TRUE) {
                header("Location: obat.php");
                exit;
            } else {
                $error_message = "Terjadi kesalahan: " . $conn->error;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Obat - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Obat</h1>

    <!-- Menampilkan pesan error -->
    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Form Edit Obat -->
<form method="POST" action="" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-md space-y-4">
        <div>
            <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
            <input type="text" name="nama_obat" id="nama_obat" value="<?php echo htmlspecialchars($obat['nama_obat_222233']); ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg" placeholder="Nama obat">
        </div>

        <div>
            <label for="jenis_obat" class="block text-sm font-medium text-gray-700">Jenis Obat</label>
            <input type="text" name="jenis_obat" id="jenis_obat" value="<?php echo htmlspecialchars($obat['jenis_obat_222233']); ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg" placeholder="Jenis obat">
        </div>

        <div>
            <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
            <input type="number" name="stok" id="stok" value="<?php echo htmlspecialchars($obat['stok_222233']); ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg" placeholder="Jumlah stok" min="1">
        </div>

        <div>
            <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
            <input type="number" name="harga" id="harga" value="<?php echo htmlspecialchars($obat['harga_222233']); ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg" placeholder="Harga obat" min="0" step="0.01">
        </div>

        <div>
            <label for="tanggal_kadaluarsa" class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa</label>
            <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa" value="<?php echo htmlspecialchars($obat['tanggal_kadaluarsa_222233']); ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="kategori" id="kategori" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                <option value="Tablet" <?php echo ($obat['kategori_222233'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                <option value="Sirup" <?php echo ($obat['kategori_222233'] == 'Sirup') ? 'selected' : ''; ?>>Sirup</option>
                <option value="Kapsul" <?php echo ($obat['kategori_222233'] == 'Kapsul') ? 'selected' : ''; ?>>Kapsul</option>
                <option value="Salep" <?php echo ($obat['kategori_222233'] == 'Salep') ? 'selected' : ''; ?>>Salep</option>
                <option value="Obat Tetes" <?php echo ($obat['kategori_222233'] == 'Obat Tetes') ? 'selected' : ''; ?>>Obat Tetes</option>
                <option value="Injeksi" <?php echo ($obat['kategori_222233'] == 'Injeksi') ? 'selected' : ''; ?>>Injeksi</option>
            </select>
        </div>
        <div>
    <label class="block text-sm font-medium text-gray-700">Gambar Saat Ini</label>
    <img src="../uploads/<?php echo htmlspecialchars($obat['gambar_obat_222233']); ?>" alt="Gambar Obat" class="h-32 mt-2 rounded-md shadow">
</div>

<div>
    <label for="gambar" class="block text-sm font-medium text-gray-700">Ganti Gambar (opsional)</label>
    <input type="file" name="gambar" id="gambar" accept="image/*" class="w-full mt-1">
</div>


        <div class="flex justify-end">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

</body>
</html>
