<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$supplier_id = $_SESSION['user_id'];
$message = '';

// Ambil daftar pengiriman
$data_query = "SELECT p.*, o.nama_obat_222233 
               FROM pengiriman_obat_222233 p
               JOIN obat_222233 o ON p.obat_id_222233 = o.obat_id_222233
               ORDER BY p.tanggal_pengiriman_222233 DESC";
$data_result = mysqli_query($conn, $data_query);
$pengiriman_data = mysqli_fetch_all($data_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengiriman Obat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Manajemen Pengiriman Obat</h1>

    <div class="mb-4">
        <a href="tambah_pengiriman_obat.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Pengiriman Obat
        </a>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-blue-100 text-blue-800 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Pengiriman -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
<table class="min-w-full text-sm text-left">
    <thead class="bg-teal-600 text-white">
        <tr>
            <th class="p-4">No</th> <!-- Kolom No ditambahkan -->
            <th class="p-4">ID Pengiriman</th>
            <th class="p-4">Nama Obat</th>
            <th class="p-4">Gambar</th>

            <th class="p-4">Jumlah</th>
            <th class="p-4">Tanggal Pengiriman</th>
            <th class="p-4">Status</th>
            <th class="p-4">Aksi</th>
        </tr>
    </thead>
    <tbody class="text-gray-800">
        <?php if (empty($pengiriman_data)): ?>
            <tr>
                <td colspan="7" class="text-center py-6 text-gray-500">Belum ada data pengiriman obat.</td>
            </tr>
        <?php else: ?>
            <?php $no = 1; ?>
            <?php foreach ($pengiriman_data as $pengiriman): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4"><?php echo $no++; ?></td> <!-- Tampilkan nomor urut -->
                    <td class="p-4"><?php echo htmlspecialchars($pengiriman['pengiriman_id_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pengiriman['nama_obat_222233']); ?></td>
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
         <td class="p-4"><?php echo htmlspecialchars($pengiriman['jumlah_222233']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($pengiriman['tanggal_pengiriman_222233']); ?></td>
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
                            <?php echo ucfirst(htmlspecialchars($pengiriman['status_pengiriman_222233'])); ?>
                        </span>
                    </td>
                    <td class="p-4 gap-2">
                        <?php if ($pengiriman['status_pengiriman_222233'] === 'diproses'): ?>
                            <a href="edit_pengiriman.php?id=<?php echo $pengiriman['pengiriman_id_222233']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <button onclick="openModal(<?php echo $pengiriman['pengiriman_id_222233']; ?>)" class="text-red-600 hover:underline">Hapus</button>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm italic">Terkunci</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

    </div>
</div>
<!-- Modal Konfirmasi Hapus -->
<div id="modal_hapus" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden justify-center items-center">
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
<!-- Modal popup untuk perbesar gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <img id="popupImage" src="" alt="Popup Gambar" class="max-h-[80%] max-w-[90%] rounded shadow-lg">
    <button onclick="closeImagePopup()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-red-400">&times;</button>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById('modal_hapus');
        const link = document.getElementById('hapus_link');
        link.href = `hapus_pengiriman.php?id=${id}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('modal_hapus');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
   function showImagePopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImagePopup() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('popupImage').src = '';
    }

    function openModal(id) {
        const modal = document.getElementById('modal_hapus');
        const hapusLink = document.getElementById('hapus_link');
        hapusLink.href = `hapus_pengiriman.php?id=${id}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('modal_hapus');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function closeRelasiModal() {
        document.getElementById('modal_relasi').classList.add('hidden');
    }
</script>

</body>

</html>
