<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data pembeli dari tabel pembeli_222233 dan join ke users_222233
$pembeli_data = [];
$sql = "SELECT p.*, u.username_222233, u.email_222233, u.nama_222233, u.created_at_222233
        FROM pembeli_222233 p
        JOIN users_222233 u ON p.user_id_222233 = u.user_id_222233";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $pembeli_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pembeli - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Data Pembeli</h1>
        <!-- Tombol Tambah -->
    <div class="mb-4">
        <a href="tambah_pembeli.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Pembeli
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
            <th class="p-4">Alamat</th>
            <th class="p-4">Tanggal Registrasi</th>
            <th class="p-4">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($pembeli_data)): ?>
            <tr>
                <td colspan="8" class="text-center py-6 text-gray-500">Belum ada data pembeli.</td>
            </tr>
        <?php else: ?>
            <?php $no = 1; foreach ($pembeli_data as $pembeli): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?php echo $no++; ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['user_id_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['nama_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['username_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['email_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['alamat_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pembeli['created_at_222233']); ?></td>
                    <td class="p-4 flex gap-2">
                        <a href="edit_pembeli.php?id=<?php echo $pembeli['pembeli_id_222233']; ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="#" 
                           class="text-red-600 hover:underline" 
                           onclick="openModal('hapus_pembeli.php?id=<?php echo $pembeli['pembeli_id_222233']; ?>'); return false;">
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
<div id="modal_hapus" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-auto space-y-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold text-gray-800 text-center">Konfirmasi Hapus</h2>
        <p class="text-gray-700 text-center">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="flex justify-center space-x-4 mt-4">
            <a id="hapus_link" href="#" 
               class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
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
