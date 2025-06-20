<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data kasir dari kasir_222233 JOIN users_222233
$kasir_data = [];
$sql = "SELECT k.*, u.nama_222233, u.username_222233, u.email_222233, u.created_at_222233 
        FROM kasir_222233 k
        JOIN users_222233 u ON k.user_id_222233 = u.user_id_222233";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $kasir_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kasir - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Data Kasir</h1>

    <!-- Tombol Tambah -->
    <div class="mb-4">
        <a href="tambah_kasir.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Kasir
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
       <table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th>
            <th class="p-4">User ID</th>
            <th class="p-4">Nama</th>
            <th class="p-4">Username</th>
            <th class="p-4">Email</th>
            <th class="p-4">Tanggal Registrasi</th>
            <th class="p-4">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($kasir_data)): ?>
            <tr>
                <td colspan="7" class="text-center py-6 text-gray-500">Belum ada data kasir.</td>
            </tr>
        <?php else: ?>
            <?php $no = 1; foreach ($kasir_data as $kasir): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?= $no++ ?></td>
                    <td class="p-4"><?= htmlspecialchars($kasir['user_id_222233']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($kasir['nama_222233']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($kasir['username_222233']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($kasir['email_222233']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($kasir['created_at_222233']) ?></td>
                    <td class="p-4 flex gap-2">
                        <a href="edit_kasir.php?id=<?= $kasir['kasir_id_222233'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="#" 
                           class="text-red-600 hover:underline" 
                           onclick="openModal('hapus_kasir.php?id=<?= $kasir['kasir_id_222233'] ?>'); return false;">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

    </div>
</div>

<!-- Modal konfirmasi hapus -->
<div id="modal_hapus" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-auto space-y-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold text-gray-800 text-center">Konfirmasi Hapus</h2>
        <p class="text-gray-700 text-center">Apakah Anda yakin ingin menghapus kasir ini?</p>
        <div class="flex justify-center space-x-4 mt-4">
            <a id="hapus_link" href="#" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Hapus
            </a>
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
function openModal(href) {
    const modal = document.getElementById('modal_hapus');
    const link = document.getElementById('hapus_link');
    link.href = href;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('modal_hapus');
    modal.classList.add('hidden');
}
</script>

</body>
</html>
