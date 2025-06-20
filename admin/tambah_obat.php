<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $obat_id = time(); // ID unik
    $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
    $jenis_obat = mysqli_real_escape_string($conn, $_POST['jenis_obat']);
    $stok = (int)$_POST['stok'];
    $harga = (float)$_POST['harga'];
    $tanggal_kadaluarsa = $_POST['tanggal_kadaluarsa'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $gambar = '';

    // Validasi
    if (empty($nama_obat) || empty($jenis_obat) || empty($stok) || empty($harga) || empty($tanggal_kadaluarsa) || empty($kategori)) {
        $error_message = "Semua field harus diisi!";
    } else {
        // Upload gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $nama_file = $_FILES['gambar']['name'];
            $tmp_file = $_FILES['gambar']['tmp_name'];
            $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            $nama_baru = 'obat_' . uniqid() . '.' . $ext;

            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed_ext)) {
                $error_message = "Ekstensi file tidak didukung.";
            } else {
                $upload_dir = dirname(__DIR__) . '/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (!is_writable($upload_dir)) {
                    $error_message = "Folder upload tidak bisa ditulis.";
                } else {
                    $path_simpan = $upload_dir . $nama_baru;
                    if (move_uploaded_file($tmp_file, $path_simpan)) {
                        $gambar = $nama_baru;
                    } else {
                        $error_message = "Gagal upload gambar.";
                    }
                }
            }
        }

        // Simpan ke database
        if (!isset($error_message)) {
            $query = "INSERT INTO obat_222233 
                (obat_id_222233, nama_obat_222233, jenis_obat_222233, stok_222233, harga_222233, tanggal_kadaluarsa_222233, kategori_222233, gambar_obat_222233)
                VALUES (
                    '$obat_id',
                    '$nama_obat',
                    '$jenis_obat',
                    '$stok',
                    '$harga',
                    '$tanggal_kadaluarsa',
                    '$kategori',
                    '$gambar'
                )";

            if (mysqli_query($conn, $query)) {
                header("Location: obat.php");
                exit;
            } else {
                $error_message = "Gagal menyimpan ke database: " . mysqli_error($conn);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Obat Baru</h1>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-md space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Obat</label>
                <input type="text" name="nama_obat" required class="w-full px-4 py-2 border rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Jenis Obat</label>
                <input type="text" name="jenis_obat" required class="w-full px-4 py-2 border rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" name="stok" min="1" required class="w-full px-4 py-2 border rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Harga</label>
                <input type="number" name="harga" step="0.01" min="0" required class="w-full px-4 py-2 border rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa</label>
                <input type="date" name="tanggal_kadaluarsa" required class="w-full px-4 py-2 border rounded-lg" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="kategori" required class="w-full px-4 py-2 border rounded-lg bg-white">
                    <option value="Tablet">Tablet</option>
                    <option value="Sirup">Sirup</option>
                    <option value="Kapsul">Kapsul</option>
                    <option value="Salep">Salep</option>
                    <option value="Obat Tetes">Obat Tetes</option>
                    <option value="Injeksi">Injeksi</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Gambar Obat</label>
                <input type="file" name="gambar" accept="image/*" class="w-full px-4 py-2 border rounded-lg bg-white" />
            </div>

            <div class="flex justify-end gap-4">
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700">Simpan</button>
                <a href="obat.php" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
