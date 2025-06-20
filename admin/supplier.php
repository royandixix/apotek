<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data supplier dari tabel supplier_222233 dan join ke users_222233
$supplier_data = [];
$sql = "SELECT s.*, u.username_222233, u.email_222233, u.nama_222233 
        FROM supplier_222233 s 
        JOIN users_222233 u ON s.user_id_222233 = u.user_id_222233";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $supplier_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Supplier - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700 mb-6">Manajemen Data Supplier</h1>
            <!-- Tombol Tambah -->
    <div class="mb-4">
        <a href="tambah_supplier.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Supplier
        </a>
    </div>
    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
<table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th>
            <th class="p-4">User ID</th>
            <th class="p-4">Username</th>
            <th class="p-4">Nama Supplier</th>
            <th class="p-4">Nama Perusahaan</th>
            <th class="p-4">Alamat</th>
            <th class="p-4">No. Telp</th>
            <th class="p-4">Email</th>
            <th class="p-4 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($supplier_data)): ?>
            <tr>
                <td colspan="9" class="text-center py-6 text-gray-500">Belum ada data supplier.</td>
            </tr>
        <?php else: ?>
            <?php $no = 1; foreach ($supplier_data as $supplier): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?php echo $no++; ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['user_id_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['username_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['nama_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['nama_perusahaan_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['alamat_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['no_telp_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($supplier['email_222233']); ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="edit_supplier.php?id=<?php echo $supplier['supplier_id_222233']; ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="javascript:void(0);" onclick="openModal('<?php echo $supplier['supplier_id_222233']; ?>')" class="text-red-600 hover:underline">Hapus</a>
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
        <p class="text-gray-700 text-center">Apakah Anda yakin ingin menghapus supplier ini?</p>
        <div class="flex justify-center space-x-4 mt-4">
            <!-- Link Hapus yang akan diatur via JS -->
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
</body>
<script>
    // Fungsi untuk membuka modal
    function openModal(supplierId) {
        document.getElementById('modal_hapus').classList.remove('hidden');
        
        // Set link hapus dengan ID supplier yang sesuai
        document.getElementById('hapus_link').setAttribute('href', 'hapus_supplier.php?id=' + supplierId);
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('modal_hapus').classList.add('hidden');
    }
</script>
</html>
