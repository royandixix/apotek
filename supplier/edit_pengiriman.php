<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: pengiriman_obat.php");
    exit();
}

$pengiriman_id = (int)$_GET['id'];
$message = '';
$user_id = $_SESSION['user_id'];

// Ambil detail pengiriman + nama obat
$sql = "SELECT p.*, o.nama_obat_222233 
        FROM pengiriman_obat_222233 p
        JOIN obat_222233 o ON p.obat_id_222233 = o.obat_id_222233
        WHERE p.pengiriman_id_222233 = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pengiriman_id);
$stmt->execute();
$result = $stmt->get_result();
$pengiriman = $result->fetch_assoc();
$stmt->close();

if (!$pengiriman) {
    $message = "Data pengiriman tidak ditemukan.";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_pengiriman = $_POST['tanggal_pengiriman'];
    $status_pengiriman = $_POST['status_pengiriman'];

    $stmt = $conn->prepare("UPDATE pengiriman_obat_222233 
                            SET tanggal_pengiriman_222233 = ?, status_pengiriman_222233 = ? 
                            WHERE pengiriman_id_222233 = ?");
    $stmt->bind_param("ssi", $tanggal_pengiriman, $status_pengiriman, $pengiriman_id);

    if ($stmt->execute()) {
        header("Location: pengiriman_obat.php?message=Pengiriman berhasil diperbarui.");
        exit();
    } else {
        $message = "Gagal memperbarui pengiriman: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengiriman Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Pengiriman Obat</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php elseif ($pengiriman): ?>
        <form method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Obat</label>
                <input type="text" value="<?= htmlspecialchars($pengiriman['nama_obat_222233']); ?>" readonly
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" value="<?= htmlspecialchars($pengiriman['jumlah_222233']); ?>" readonly
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div>
                <label for="tanggal_pengiriman" class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
                <input type="date" name="tanggal_pengiriman" id="tanggal_pengiriman"
                       value="<?= htmlspecialchars($pengiriman['tanggal_pengiriman_222233']); ?>" required
                       min="<?= date('Y-m-d'); ?>"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label for="status_pengiriman" class="block text-sm font-medium text-gray-700">Status Pengiriman</label>
                <select name="status_pengiriman" id="status_pengiriman" required
                        class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
                    <option value="diproses" <?= $pengiriman['status_pengiriman_222233'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="dikirim" <?= $pengiriman['status_pengiriman_222233'] === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <!-- <option value="diterima" <?= $pengiriman['status_pengiriman_222233'] === 'diterima' ? 'selected' : '' ?>>Diterima</option> -->
                </select>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                    Simpan Perubahan
                </button>
                <a href="pengiriman_obat.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                    Batal
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
