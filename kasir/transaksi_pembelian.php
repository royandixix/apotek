<?php
session_start();
include '../koneksi.php';

// Cek login dan role kasir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}

// Ambil kasir_id dari user_id
$kasir_id = null;
$user_id = $_SESSION['user_id'];
$stmt_kasir = $conn->prepare("SELECT kasir_id_222233 FROM kasir_222233 WHERE user_id_222233 = ?");
$stmt_kasir->bind_param("i", $user_id);
$stmt_kasir->execute();
$result_kasir = $stmt_kasir->get_result();
if ($row = $result_kasir->fetch_assoc()) {
    $kasir_id = $row['kasir_id_222233'];
} else {
    die("❌ Kasir tidak ditemukan.");
}
$stmt_kasir->close();

// Proses update status transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaksi_id = $_POST['transaksi_id'];
    $status = $_POST['status_transaksi'];

    $status_pembayaran = match($status) {
        'pending' => 'belum_dibayar',
        'sukses' => 'sudah_dibayar',
        'batal' => 'dibatalkan',
        default => 'belum_dibayar',
    };

    if ($status === 'batal') {
        // Jika batal, jangan set kasir_id
        $stmt = $conn->prepare("UPDATE transaksi_222233 SET status_transaksi_222233 = ? WHERE transaksi_id_222233 = ?");
        $stmt->bind_param("si", $status, $transaksi_id);
    } else {
        // Jika sukses/pending, isi kasir_id
        $stmt = $conn->prepare("UPDATE transaksi_222233 SET status_transaksi_222233 = ?, kasir_id_222233 = ? WHERE transaksi_id_222233 = ?");
        $stmt->bind_param("sii", $status, $kasir_id, $transaksi_id);
    }
    $stmt->execute();
    $stmt->close();

    // Update status pembayaran
    $stmt2 = $conn->prepare("UPDATE pembayaran_222233 SET status_pembayaran_222233 = ? WHERE transaksi_id_222233 = ?");
    $stmt2->bind_param("si", $status_pembayaran, $transaksi_id);
    $stmt2->execute();
    $stmt2->close();

    $_SESSION['pesan'] = '<div class="bg-green-100 text-green-800 p-3 rounded">Status berhasil diperbarui.</div>';
    header("Location: transaksi_pembelian.php");
    exit;
}

// Ambil data transaksi
$stmt = $conn->prepare("
    SELECT t.*, p.metode_pembayaran_222233, p.status_pembayaran_222233,
           p.jumlah_pembayaran_222233, p.tanggal_pembayaran_222233
    FROM transaksi_222233 t
    LEFT JOIN pembayaran_222233 p ON t.transaksi_id_222233 = p.transaksi_id_222233
    WHERE t.kasir_id_222233 IS NULL OR t.kasir_id_222233 = ?
    ORDER BY t.tanggal_transaksi_222233 DESC
");
$stmt->bind_param("i", $kasir_id);
$stmt->execute();
$result = $stmt->get_result();
$transaksi_data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Transaksi Pembelian</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <?php echo $pesan; ?>

    <h1 class="text-3xl font-bold text-teal-700 mb-6">Manajemen Transaksi Pembelian</h1>

    <?php if ($kasir_id): ?>
        <!-- Tabel Transaksi -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
            <table class="min-w-full text-sm text-left">
<thead class="bg-teal-600 text-white">
    <tr>
        <th class="p-4">ID Transaksi</th>
        <th class="p-4">Total Harga</th>
        <th class="p-4">Tanggal</th>
        <th class="p-4">Status</th>
        <th class="p-4">Metode</th>
        <th class="p-4">Status Bayar</th>
        <th class="p-4 text-right">Jumlah Bayar</th>
        <th class="p-4">Tgl Bayar</th>
        <th class="p-4 text-center">Aksi</th>
    </tr>
</thead>

                <tbody class="text-gray-800">
    <?php if (empty($transaksi_data)): ?>
        <tr>
            <td colspan="7" class="text-center py-6 text-gray-500">Belum ada data transaksi.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($transaksi_data as $transaksi): ?>
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="p-4"><?php echo htmlspecialchars($transaksi['transaksi_id_222233']); ?></td>
                <td class="p-4">Rp <?php echo number_format($transaksi['total_harga_222233'], 2, ',', '.'); ?></td>
                <td class="p-4"><?php echo htmlspecialchars($transaksi['tanggal_transaksi_222233']); ?></td>
                <td class="p-4">
                    <form action="transaksi_pembelian.php" method="POST">
                        <input type="hidden" name="transaksi_id" value="<?php echo $transaksi['transaksi_id_222233']; ?>">
                        <select name="status_transaksi" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="pending" <?php echo ($transaksi['status_transaksi_222233'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="sukses" <?php echo ($transaksi['status_transaksi_222233'] == 'sukses') ? 'selected' : ''; ?>>Sukses</option>
                            <option value="batal" <?php echo ($transaksi['status_transaksi_222233'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                        </select>
                    </form>
                </td>
                <td class="p-4"><?php echo ucfirst($transaksi['metode_pembayaran_222233'] ?? '-'); ?></td>
                <td class="p-4"><?php echo ucfirst(str_replace('_', ' ', $transaksi['status_pembayaran_222233'] ?? '-')); ?></td>
                <td class="p-4 text-right">Rp <?php echo number_format($transaksi['jumlah_pembayaran_222233'], 2, ',', '.'); ?></td>
                <td class="p-4"><?php echo $transaksi['tanggal_pembayaran_222233'] ?? '-'; ?></td>
<td class="p-4 text-center space-y-1">
<a href="javascript:void(0);" 
   onclick="cetakResi(<?php echo $transaksi['transaksi_id_222233']; ?>)"
   class="text-green-600 hover:underline text-sm">
   Cetak Resi
</a>

    <a href="javascript:void(0);" 
       class="text-red-600 hover:underline text-sm"
       onclick="openModal('hapus_pembelian.php?delete_id=<?php echo $transaksi['transaksi_id_222233']; ?>')">
        Hapus
    </a>
</td>

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

            </table>
        </div>
    <?php else: ?>
        <p class="text-red-600">Profil kasir belum lengkap. Hubungi admin.</p>
    <?php endif; ?>
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
            <button onclick="closeModal()" 
                    class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
    function openModal(deleteUrl) {
        const modal = document.getElementById('modal_hapus');
        const hapusLink = document.getElementById('hapus_link');
        hapusLink.href = deleteUrl;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('modal_hapus');
        modal.classList.add('hidden');
    }
    function cetakResi(transaksiId) {
    const cetakWindow = window.open('cetak_resi.php?transaksi_id=' + transaksiId, '_blank', 'width=700,height=600');

    cetakWindow.onload = function() {
        cetakWindow.print();
    };
}
</script>

</body>
</html>
