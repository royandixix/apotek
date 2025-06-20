<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses update status pengiriman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pengiriman_id'], $_POST['status_pengiriman'])) {
    $pengiriman_id = intval($_POST['pengiriman_id']);
    $status = $_POST['status_pengiriman'];

    $stmt = $conn->prepare("UPDATE pengiriman_obat_222233 SET status_pengiriman_222233 = ? WHERE pengiriman_id_222233 = ?");
    $stmt->bind_param("si", $status, $pengiriman_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pesan'] = '<div class="bg-green-100 text-green-800 p-3 rounded">Status pengiriman berhasil diperbarui.</div>';
    header("Location: pengiriman.php");
    exit;
}

$pengiriman_data = [];
$sql = "
    SELECT p.*, o.nama_obat_222233, u.nama_222233 AS nama_supplier
    FROM pengiriman_obat_222233 p
    LEFT JOIN obat_222233 o ON p.obat_id_222233 = o.obat_id_222233
    LEFT JOIN supplier_222233 s ON p.supplier_id_222233 = s.supplier_id_222233
    LEFT JOIN users_222233 u ON s.user_id_222233 = u.user_id_222233
    ORDER BY p.tanggal_pengiriman_222233 DESC
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $pengiriman_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengiriman Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Data Pengiriman Obat</h1>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="mb-4"><?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
        <table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th>
            <th class="p-4">Nama Obat</th>
            <th class="p-4">Gambar</th>

            <th class="p-4">Jumlah</th>
            <th class="p-4">Tanggal</th>
            <th class="p-4">Supplier</th>
            <th class="p-4">Penawaran ID</th>
            <th class="p-4">Status</th>
            <th class="p-4 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($pengiriman_data)): ?>
            <tr><td colspan="9" class="text-center p-6 text-gray-500">Belum ada data pengiriman.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach ($pengiriman_data as $pengiriman): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?= $no++ ?></td>
                    <td class="p-4"><?= htmlspecialchars($pengiriman['nama_obat_222233'] ?? '-') ?></td>
                    <td class="p-4">
    <?php if (!empty($pengiriman['gambar_obat_222233'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($pengiriman['gambar_obat_222233']); ?>" 
             alt="Gambar Obat" 
             class="w-12 h-12 object-cover rounded cursor-pointer"
             onclick="showImagePopup('../uploads/<?php echo htmlspecialchars($pengiriman['gambar_obat_222233']); ?>')">
    <?php else: ?>
        <span class="text-gray-400 italic">Tidak ada</span>
    <?php endif; ?>
</td>

                    <td class="p-4"><?= $pengiriman['jumlah_222233'] ?></td>
                    <td class="p-4"><?= date("d-m-Y", strtotime($pengiriman['tanggal_pengiriman_222233'])) ?></td>
                    <td class="p-4"><?= htmlspecialchars($pengiriman['nama_supplier'] ?? '-') ?></td>
                    <td class="p-4"><?= $pengiriman['penawaran_id_222233'] ?></td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            <?php
                                echo match($pengiriman['status_pengiriman_222233']) {
                                    'diproses' => 'bg-yellow-100 text-yellow-800',
                                    'dikirim' => 'bg-blue-100 text-blue-800',
                                    'diterima' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            ?>">
                            <?= $pengiriman['status_pengiriman_222233'] ?>
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <form method="POST" class="inline-block space-x-1">
                            <input type="hidden" name="pengiriman_id" value="<?= $pengiriman['pengiriman_id_222233'] ?>">
                            <select name="status_pengiriman" class="border rounded px-2 py-1 text-sm">
                                <option value="diproses" <?= $pengiriman['status_pengiriman_222233'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikirim" <?= $pengiriman['status_pengiriman_222233'] === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                <option value="diterima" <?= $pengiriman['status_pengiriman_222233'] === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                            </select>
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded text-sm">Ubah</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<!-- Modal popup gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <img id="popupImage" src="" alt="Popup Gambar" class="max-h-[80%] max-w-[90%] rounded shadow-lg">
    <button onclick="closeImagePopup()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-red-400">&times;</button>
</div>
<script>
    function showImagePopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImagePopup() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('popupImage').src = '';
    }
</script>

    </div>
</div>
</body>
</html>
