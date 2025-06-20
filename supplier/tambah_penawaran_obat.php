<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$supplier_id = null;
$message = '';

// Ambil supplier_id dari user_id
$stmt = $conn->prepare("SELECT supplier_id_222233 FROM supplier_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $supplier_id = $row['supplier_id_222233'];
} else {
    die("Supplier tidak ditemukan untuk user ini.");
}
$stmt->close();

// Daftar kategori
$kategori_options = ['Tablet','Sirup','Kapsul','Salep','Obat Tetes','Injeksi'];

// Ambil daftar obat
$obat_list = [];
$query = "SELECT * FROM obat_222233";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $obat_list[] = $row;
}

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $penawaran_type = $_POST['penawaran_type'];

    if ($penawaran_type === 'existing') {
        $obat_id = (int)$_POST['obat_id'];
        $jumlah = (int)$_POST['jumlah'];
        $harga_satuan = (float)$_POST['harga_satuan'];

        $stmt = $conn->prepare("SELECT nama_obat_222233, jenis_obat_222233, kategori_222233 FROM obat_222233 WHERE obat_id_222233 = ?");
        $stmt->bind_param("i", $obat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $obat = $result->fetch_assoc();
        $stmt->close();

        if ($obat && $jumlah > 0 && $harga_satuan > 0) {
            $nama_obat = $obat['nama_obat_222233'];
            $jenis_obat = $obat['jenis_obat_222233'];
            $kategori = $obat['kategori_222233'];

            $query = "INSERT INTO penawaran_obat_222233 
                      (supplier_id_222233, nama_obat_222233, jenis_obat_222233, kategori_222233, jumlah_222233, harga_satuan_222233) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssid", $supplier_id, $nama_obat, $jenis_obat, $kategori, $jumlah, $harga_satuan);
            if ($stmt->execute()) {
                header("Location: penawaran_obat.php?message=Sukses menambahkan penawaran.");
                exit();
            } else {
                $message = "Gagal menambahkan penawaran: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Obat tidak ditemukan atau input tidak valid.";
        }

    } elseif ($penawaran_type === 'new') {
        $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
        $jenis_obat = mysqli_real_escape_string($conn, $_POST['jenis_obat']);
        $kategori = $_POST['kategori'];
        $jumlah = (int)$_POST['jumlah'];
        $harga_satuan = (float)$_POST['harga_satuan'];

        $gambar_obat = null;

        // Upload gambar terlebih dahulu
        if (isset($_FILES['gambar_obat']) && $_FILES['gambar_obat']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $tmpName = $_FILES['gambar_obat']['tmp_name'];
            $originalName = basename($_FILES['gambar_obat']['name']);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $newFileName = uniqid('obat_', true) . '.' . $extension;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $gambar_obat = $newFileName;
            }
        }

        if ($nama_obat && $jenis_obat && in_array($kategori, $kategori_options) && $jumlah > 0 && $harga_satuan > 0) {
            $query = "INSERT INTO penawaran_obat_222233 
                      (supplier_id_222233, nama_obat_222233, jenis_obat_222233, kategori_222233, jumlah_222233, harga_satuan_222233, gambar_obat_222233) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssids", $supplier_id, $nama_obat, $jenis_obat, $kategori, $jumlah, $harga_satuan, $gambar_obat);
            if ($stmt->execute()) {
                header("Location: penawaran_obat.php?message=Sukses menambahkan penawaran.");
                exit();
            } else {
                $message = "Gagal menambahkan penawaran: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Semua kolom harus diisi dengan benar dan kategori harus valid.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penawaran Obat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Penawaran Obat</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md space-y-4">

        <!-- Tipe Penawaran -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penawaran</label>
            <div class="flex gap-4">
                <label class="flex items-center">
                    <input type="radio" name="penawaran_type" value="existing" checked onclick="toggleForm(true)" class="mr-2">
                    Gunakan Obat yang Sudah Ada
                </label>
                <label class="flex items-center">
                    <input type="radio" name="penawaran_type" value="new" onclick="toggleForm(false)" class="mr-2">
                    Tambah Obat Baru
                </label>
            </div>
        </div>

        <!-- Pilih Obat dari Database -->
        <div id="existingObatSection">
            <label for="obat_id" class="block text-sm font-medium text-gray-700">Pilih Obat</label>
            <select name="obat_id" id="obat_id"
                    class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                <option value="" disabled selected>Pilih Obat</option>
                <?php foreach ($obat_list as $obat): ?>
                    <option value="<?= $obat['obat_id_222233'] ?>">
                        <?= htmlspecialchars($obat['nama_obat_222233']) ?> - <?= $obat['jenis_obat_222233'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Input Manual Obat Baru -->
        <div id="newObatSection" style="display: none;">
            <div>
                <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
                <input type="text" name="nama_obat" id="nama_obat"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                       placeholder="Nama obat">
            </div>

            <div>
                <label for="jenis_obat" class="block text-sm font-medium text-gray-700">Jenis Obat</label>
                <input type="text" name="jenis_obat" id="jenis_obat"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                       placeholder="Jenis obat (misal: Antibiotik)">
            </div>

            <div>
                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="kategori" id="kategori"
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                    <option value="" disabled selected>Pilih kategori</option>
                    <?php foreach ($kategori_options as $k): ?>
                        <option value="<?= $k ?>"><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
    <label for="gambar_obat" class="block text-sm font-medium text-gray-700">Gambar Obat (Opsional)</label>
    <input type="file" name="gambar_obat" id="gambar_obat"
           class="w-full mt-1 border border-gray-300 rounded-lg px-4 py-2"
           accept="image/*">
</div>

        </div>

        <!-- Input Jumlah dan Harga -->
        <div>
            <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" required min="1"
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   placeholder="Jumlah penawaran">
        </div>

        <div>
            <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
            <input type="number" name="harga_satuan" id="harga_satuan" required min="0" step="0.01"
                   class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg"
                   placeholder="Harga per satuan">
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end space-x-4">
            <button type="submit"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                Simpan
            </button>
            <a href="penawaran_obat.php"
               class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function toggleForm(isExisting) {
    document.getElementById('existingObatSection').style.display = isExisting ? 'block' : 'none';
    document.getElementById('newObatSection').style.display = isExisting ? 'none' : 'block';
}
</script>

</body>
</html>
