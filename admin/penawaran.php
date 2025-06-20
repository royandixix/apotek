<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses update status penawaran jika dikirim melalui form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['penawaran_id'], $_POST['status_penawaran'])) {
    $penawaran_id = intval($_POST['penawaran_id']);
    $status = $_POST['status_penawaran'];

    $stmt = $conn->prepare("UPDATE penawaran_obat_222233 SET status_penawaran_222233 = ? WHERE penawaran_id_222233 = ?");
    $stmt->bind_param("si", $status, $penawaran_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['pesan'] = '<div class="bg-green-100 text-green-800 p-3 rounded">Status penawaran berhasil diperbarui.</div>';
    header("Location: penawaran.php");
    exit;
}

// Ambil semua penawaran
$penawaran_data = [];
$sql = "SELECT * FROM penawaran_obat_222233 ORDER BY tanggal_penawaran_222233 DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $penawaran_data[] = $row;
}
$query = "
SELECT 
    p.*,
    u.nama_222233 AS nama_supplier
FROM penawaran_obat_222233 p
JOIN supplier_222233 s ON p.supplier_id_222233 = s.supplier_id_222233
JOIN users_222233 u ON s.user_id_222233 = u.user_id_222233
ORDER BY p.tanggal_penawaran_222233 DESC
";

$result = $conn->query($query);
$penawaran_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $penawaran_data[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Penawaran Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Data Penawaran Obat</h1>

    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="mb-4">
            <?php
                echo $_SESSION['pesan'];
                unset($_SESSION['pesan']);
            ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
       <table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th>
            <!-- <th class="p-4">ID</th> -->
            <th class="p-4">Supplier</th>
            <th class="p-4">Nama Obat</th>
            <th class="p-4">Gambar</th>

            <th class="p-4">Jenis</th>
            <th class="p-4">Kategori</th>
            <th class="p-4">Jumlah</th>
            <th class="p-4">Harga Satuan</th>
            <th class="p-4">Tanggal</th>
            <th class="p-4">Status</th>
            <th class="p-4 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($penawaran_data)): ?>
            <tr><td colspan="11" class="text-center p-6 text-gray-500">Belum ada data penawaran.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach ($penawaran_data as $penawaran): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?= $no++ ?></td>
                    <!-- <td class="p-4"><?= htmlspecialchars($penawaran['penawaran_id_222233']) ?></td> -->
                    <td class="p-4"><?= htmlspecialchars($penawaran['nama_supplier']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($penawaran['nama_obat_222233']) ?></td>
<td class="p-4">
    <?php if (!empty($penawaran['gambar_obat_222233'])): ?>
        <img src="../uploads/<?= htmlspecialchars($penawaran['gambar_obat_222233']) ?>"
             alt="Gambar Obat"
             class="h-12 w-12 object-cover cursor-pointer rounded border"
             onclick="showImagePopup('../uploads/<?= htmlspecialchars($penawaran['gambar_obat_222233']) ?>')">
    <?php else: ?>
        <span class="text-gray-400 italic">Tidak ada</span>
    <?php endif; ?>
</td>
  <td class="p-4"><?= htmlspecialchars($penawaran['jenis_obat_222233']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($penawaran['kategori_222233']) ?></td>
                    <td class="p-4"><?= $penawaran['jumlah_222233'] ?></td>
                    <td class="p-4">Rp <?= number_format($penawaran['harga_satuan_222233'], 2, ',', '.') ?></td>
                    <td class="p-4"><?= date("d-m-Y H:i", strtotime($penawaran['tanggal_penawaran_222233'])) ?></td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            <?php
                                echo match($penawaran['status_penawaran_222233']) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'diterima' => 'bg-green-100 text-green-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            ?>">
                            <?= $penawaran['status_penawaran_222233'] ?>
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <form method="POST" class="inline-block space-x-1">
                            <input type="hidden" name="penawaran_id" value="<?= $penawaran['penawaran_id_222233'] ?>">
                            <select name="status_penawaran" class="border rounded px-2 py-1 text-sm">
                                <option value="pending" <?= $penawaran['status_penawaran_222233'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="diterima" <?= $penawaran['status_penawaran_222233'] === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                <option value="ditolak" <?= $penawaran['status_penawaran_222233'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded text-sm">Ubah</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

    </div>
</div>
<!-- Modal popup untuk perbesar gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <img id="popupImage" src="" alt="Popup Gambar" class="max-h-[80%] max-w-[90%] rounded shadow-lg">
    <button onclick="closeImagePopup()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-red-400">&times;</button>
</div>


</body>
<script>
    function showImagePopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImagePopup() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('popupImage').src = '';
    }

    // Modal konfirmasi hapus (jika kamu pakai ini juga)
    function openModal(id) {
        const modal = document.getElementById('modal_hapus');
        const hapusLink = document.getElementById('hapus_link');
        hapusLink.href = `hapus_penawaran.php?id=${id}`;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal_hapus').classList.add('hidden');
    }

    function closeRelasiModal() {
        document.getElementById('modal_relasi').classList.add('hidden');
    }
</script>

</html>
