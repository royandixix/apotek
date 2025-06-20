<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data obat
$obat_data = [];
$sql = "SELECT * FROM obat_222233";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $obat_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Obat - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Konten -->
    <div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700 mb-6">Daftar Data Obat</h1>

        <!-- Tombol tambah -->
        <div class="mb-4">
            <a href="tambah_obat.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
                + Tambah Obat
            </a>
        </div>

        <!-- Tabel Obat -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="p-4">No</th>
                        <th class="p-4">Nama Obat</th>
                        <th class="p-4">Gambar</th>
                        <th class="p-4">Jenis</th>
                        <th class="p-4">Stok</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php if (empty($obat_data)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500">Belum ada data obat.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($obat_data as $obat): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-4"><?= $no++ ?></td>
                                <td class="p-4"><?= htmlspecialchars($obat['nama_obat_222233']) ?></td>
                                <td class="p-4">
                                    <?php if (!empty($obat['gambar_obat_222233'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($obat['gambar_obat_222233']) ?>"
                                            alt="Gambar Obat"
                                            class="w-12 h-12 object-cover rounded cursor-pointer"
                                            onclick="showImagePopup('../uploads/<?= htmlspecialchars($obat['gambar_obat_222233']) ?>')">
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4"><?= htmlspecialchars($obat['jenis_obat_222233']) ?></td>
                                <td class="p-4"><?= htmlspecialchars($obat['stok_222233']) ?></td>
                                <td class="p-4">Rp <?= number_format($obat['harga_222233'], 2, ',', '.') ?></td>
                                <td class="p-4">
                                    <span class="inline-block px-3 py-1 rounded-full bg-teal-100 text-teal-800 text-xs font-semibold">
                                        <?= htmlspecialchars($obat['kategori_222233']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="edit_obat.php?id=<?= $obat['obat_id_222233'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <a href="#" class="text-red-600 hover:underline" onclick="openModal('<?= $obat['obat_id_222233'] ?>')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="modal_hapus" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full space-y-4">
            <h2 class="text-xl font-semibold text-center text-gray-800">Konfirmasi Hapus</h2>
            <p class="text-center text-gray-600">Apakah Anda yakin ingin menghapus obat ini?</p>
            <div class="flex justify-center gap-4 mt-4">
                <a id="hapus_link" href="#" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Hapus</a>
                <button onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">Batal</button>
            </div>
        </div>
    </div>

    <!-- Modal Popup Gambar -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center">
        <img id="popupImage" src="" alt="Popup Gambar" class="max-h-[80%] max-w-[90%] rounded shadow-lg">
        <button onclick="closeImagePopup()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-red-400">&times;</button>
    </div>

    <!-- Script JS -->
    <script>
        function openModal(obatId) {
            document.getElementById("modal_hapus").classList.remove("hidden");
            document.getElementById("hapus_link").href = "hapus_obat.php?id=" + obatId;
        }

        function closeModal() {
            document.getElementById("modal_hapus").classList.add("hidden");
        }

        function showImagePopup(src) {
            document.getElementById("popupImage").src = src;
            document.getElementById("imageModal").classList.remove("hidden");
        }

        function closeImagePopup() {
            document.getElementById("imageModal").classList.add("hidden");
            document.getElementById("popupImage").src = '';
        }
    </script>

</body>
</html>
