<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Ambil supplier_id dari user_id
$supplier_id = null;
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
// Ambil data penawaran obat berdasarkan supplier_id dari tabel penawaran_obat_222233 langsung
$query = "
    SELECT penawaran_id_222233, nama_obat_222233, jenis_obat_222233, kategori_222233, 
           jumlah_222233, harga_satuan_222233, tanggal_penawaran_222233, status_penawaran_222233,
           gambar_obat_222233
    FROM penawaran_obat_222233
    WHERE supplier_id_222233 = ?
    ORDER BY tanggal_penawaran_222233 DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$penawaran_data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Penawaran Obat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Informasi Penawaran Obat Anda</h1>
    <div class="mb-4">
        <a href="tambah_penawaran_obat.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah Penawaran Obat
        </a>
    </div>
    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-blue-100 text-blue-800 rounded-lg">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white rounded-xl shadow-md">
        <table class="min-w-full text-sm text-left">
<thead class="bg-teal-600 text-white">
    <tr>
        <th class="p-4">No</th>
        <th class="p-4">ID</th> <!-- Kolom ID baru -->
        <th class="p-4">Nama Obat</th>
        <th class="p-4">Gambar</th> <!-- Tambah kolom gambar -->
        <th class="p-4">Jenis</th>
        <th class="p-4">Kategori</th>
        <th class="p-4">Jumlah</th>
        <th class="p-4">Harga Satuan</th>
        <th class="p-4">Tanggal</th>
        <th class="p-4">Status</th>
        <th class="p-4">Aksi</th>
    </tr>
</thead>


            <tbody class="text-gray-800">
<?php if (empty($penawaran_data)): ?>
    <tr>
        <td colspan="10" class="text-center py-6 text-gray-500">Belum ada data penawaran obat.</td>
    </tr>
<?php else: ?>
    <?php $no = 1; ?>
    <?php foreach ($penawaran_data as $penawaran): ?>
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="p-4"><?php echo $no++; ?></td>
            <td class="p-4"><?php echo htmlspecialchars($penawaran['penawaran_id_222233']); ?></td> <!-- Tampilkan ID -->
            <td class="p-4"><?php echo htmlspecialchars($penawaran['nama_obat_222233']); ?></td>
            <td class="p-4">
    <?php if (!empty($penawaran['gambar_obat_222233'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($penawaran['gambar_obat_222233']); ?>" 
             alt="Gambar Obat" 
             class="w-16 h-16 object-cover cursor-pointer rounded" 
             onclick="showImagePopup('../uploads/<?php echo htmlspecialchars($penawaran['gambar_obat_222233']); ?>')">
    <?php else: ?>
        <span class="text-gray-400 italic">Tidak ada gambar</span>
    <?php endif; ?>
</td>
            <td class="p-4"><?php echo htmlspecialchars($penawaran['jenis_obat_222233'] ?? '-'); ?></td>
            <td class="p-4"><?php echo htmlspecialchars($penawaran['kategori_222233'] ?? '-'); ?></td>
            <td class="p-4"><?php echo htmlspecialchars($penawaran['jumlah_222233']); ?></td>
            <td class="p-4">Rp <?php echo number_format($penawaran['harga_satuan_222233'], 2, ',', '.'); ?></td>
            <td class="p-4"><?php echo htmlspecialchars($penawaran['tanggal_penawaran_222233']); ?></td>
            <td class="p-4"><?php echo ucfirst(htmlspecialchars($penawaran['status_penawaran_222233'])); ?></td>
            
            <td class="p-4 gap-2">
                <?php if ($penawaran['status_penawaran_222233'] === 'pending'): ?>
                    <a href="edit_penawaran.php?id=<?php echo $penawaran['penawaran_id_222233']; ?>" class="text-blue-600 hover:underline">Edit</a>
                <?php else: ?>
                    <span class="text-gray-400 italic">Terkunci</span>
                <?php endif; ?>
                <a href="#" 
                   onclick="openModal(<?php echo $penawaran['penawaran_id_222233']; ?>)" 
                   class="text-red-600 hover:underline">
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
<!-- Modal popup untuk perbesar gambar -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <img id="popupImage" src="" alt="Popup Gambar" class="max-h-[80%] max-w-[90%] rounded shadow-lg">
    <button onclick="closeImagePopup()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-red-400">&times;</button>
</div>

<?php if (isset($_GET['relasi']) && $_GET['relasi'] == 'true'): ?>
    <div id="modal_relasi" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-auto space-y-4 flex flex-col items-center">
            <h2 class="text-xl font-semibold text-gray-800 text-center">Tidak Bisa Dihapus</h2>
            <p class="text-gray-700 text-center">Penawaran ini tidak dapat dihapus karena sudah digunakan dalam data pengiriman obat.</p>
            <button onclick="closeRelasiModal()" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Tutup
            </button>
        </div>
    </div>
<?php endif; ?>

<script>
    function openModal(id) {
        const modal = document.getElementById('modal_hapus');
        const hapusLink = document.getElementById('hapus_link');
        hapusLink.href = `hapus_penawaran.php?id=${id}`;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('modal_hapus');
        modal.classList.add('hidden');
    }
       function showImagePopup(src) {
        document.getElementById('popupImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImagePopup() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function openModal(id) {
        const modal = document.getElementById('modal_hapus');
        const hapusLink = document.getElementById('hapus_link');
        hapusLink.href = `hapus_penawaran.php?id=${id}`;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('modal_hapus');
        modal.classList.add('hidden');
    }

    function closeRelasiModal() {
        document.getElementById('modal_relasi').classList.add('hidden');
    }
</script>

</body>
</html>
