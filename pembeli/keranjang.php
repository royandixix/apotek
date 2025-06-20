<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../login.php");
    exit();
}

$message = '';

$user_id = $_SESSION['user_id'];
$query = "SELECT k.keranjang_id_222233, k.obat_id_222233, k.jumlah_222233, k.harga_222233, 
                 o.nama_obat_222233, o.jenis_obat_222233, o.stok_222233, o.gambar_obat_222233
          FROM keranjang_222233 k
          JOIN obat_222233 o ON k.obat_id_222233 = o.obat_id_222233
          WHERE k.user_id_222233 = $user_id AND k.is_deleted_222233 = 0";

$result = mysqli_query($conn, $query);
$keranjang_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (isset($_GET['delete_id'])) {
    $keranjang_id = $_GET['delete_id'];
    mysqli_query($conn, "UPDATE keranjang_222233 SET is_deleted_222233 = 1 WHERE keranjang_id_222233 = $keranjang_id");
    header("Location: keranjang.php");
    exit();
}


if (isset($_POST['checkout']) && !empty($_POST['pilih_keranjang'])) {
    $_SESSION['keranjang_terpilih'] = $_POST['pilih_keranjang'];
    header("Location: pembayaran.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jumlah'])) {
    foreach ($_POST['jumlah'] as $keranjang_id => $jumlah) {
        $keranjang_id = intval($keranjang_id);
        $jumlah = intval($jumlah);
        // Validasi jumlah (min 1)
        if ($jumlah > 0) {
            mysqli_query($conn, "UPDATE keranjang_222233 SET jumlah_222233 = $jumlah WHERE keranjang_id_222233 = $keranjang_id AND user_id_222233 = $user_id");
        }
    }
    // Refresh agar data baru muncul
    header("Location: keranjang.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Keranjang Belanja</h1>
    <div class="mb-4">
        <a href="dashboard.php" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded shadow">
            + Tambah Keranjang
        </a>
    </div>

    <form method="POST" action="keranjang.php" id="form_keranjang">
        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
            <table class="min-w-full text-sm text-left" id="tabel_keranjang">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="p-4"><input type="checkbox" id="check_all" onclick="toggleAll(this)"></th>
                        <th class="p-4">ID</th>
                        <th class="p-4">Nama Obat</th>
                        <th class="p-4">Jenis</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Jumlah</th>
                        <th class="p-4">Subtotal</th>
                        <th class="p-4">Stok</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keranjang_data)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-6 text-gray-500">Keranjang kosong.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($keranjang_data as $item): ?>
                            <?php 
                                $subtotal = $item['jumlah_222233'] * $item['harga_222233'];
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-4">
                                    <input type="checkbox" 
                                           name="pilih_keranjang[]" 
                                           value="<?php echo $item['keranjang_id_222233']; ?>" 
                                           class="checkbox_pilih" 
                                           data-subtotal="<?php echo $subtotal; ?>"
                                           onchange="updateTotal()">
                                </td>
                                <td class="p-4"><?php echo $item['keranjang_id_222233']; ?></td>
<td class="p-4">
    <span class="text-black hover:underline cursor-pointer"
          onclick="showInfoPopup(
              '<?php echo addslashes($item['nama_obat_222233']); ?>',
              '<?php echo addslashes($item['jenis_obat_222233']); ?>',
              '<?php echo addslashes($item['kategori_222233'] ?? ''); ?>',
              '<?php echo addslashes($item['stok_222233']); ?>',
              '<?php echo number_format($item['harga_222233'], 2, ',', '.'); ?>',
              '<?php echo addslashes($item['gambar_obat_222233'] ?? "default.jpg"); ?>'
          )">
        <?php echo htmlspecialchars($item['nama_obat_222233']); ?>
    </span>
</td>

                                <td class="p-4"><?php echo htmlspecialchars($item['jenis_obat_222233']); ?></td>
                                <td class="p-4">Rp <?php echo number_format($item['harga_222233'], 2, ',', '.'); ?></td>
<td class="p-4">
<input type="number"
       name="jumlah[<?php echo $item['keranjang_id_222233']; ?>]"
       value="<?php echo $item['jumlah_222233']; ?>"
       min="1"
       max="<?php echo $item['stok_222233']; ?>"
       data-id="<?php echo $item['keranjang_id_222233']; ?>"
       data-harga="<?php echo $item['harga_222233']; ?>"
       class="input_jumlah w-20 border border-gray-300 rounded px-2 py-1 text-center"
       onchange="updateSubtotal(this)">

</td>
                                <td class="p-4">Rp <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                <td class="p-4"><?php echo $item['stok_222233']; ?></td>
                                <td class="p-4">
                                    <a href="#" onclick="openModal(<?php echo $item['keranjang_id_222233']; ?>)" class="text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Total dan tombol -->
        <div class="mt-6 flex justify-between items-center">
            <div class="bg-teal-100 p-4 rounded-lg shadow">
                <span class="text-xl font-semibold text-teal-700">Total Terpilih: 
                    <span id="total_display">Rp 0,00</span>
                </span>
            </div>
            <?php if (!empty($keranjang_data)): ?>
                <button type="submit" name="checkout" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow">
                    Checkout Terpilih
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>
<!-- Modal Info Obat -->
<div id="infoModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <button onclick="closeInfoPopup()" class="absolute top-2 right-3 text-gray-600 hover:text-red-600 text-2xl font-bold">&times;</button>
        <div id="infoContent" class="text-gray-800 space-y-2">
            <!-- Konten dinamis akan ditampilkan di sini -->
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modal_hapus" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-auto space-y-4 flex flex-col items-center">
        <h2 class="text-xl font-semibold text-gray-800 text-center">Konfirmasi Hapus</h2>
        <p class="text-gray-700 text-center">Apakah Anda yakin ingin menghapus item ini dari keranjang?</p>
        <div class="flex justify-center space-x-4 mt-4">
            <a id="hapus_link" href="#" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</a>
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Batal</button>
        </div>
    </div>
</div>

<script>
function openModal(keranjangId) {
    document.getElementById('hapus_link').href = `keranjang.php?delete_id=${keranjangId}`;
    document.getElementById('modal_hapus').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modal_hapus').classList.add('hidden');
}

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.checkbox_pilih');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateTotal();
}
function updateSubtotal(input) {
    const row = input.closest('tr');
    const harga = parseFloat(input.dataset.harga);
    const jumlah = parseInt(input.value);
    const subtotal = harga * jumlah;

    // Update tampilan subtotal
    row.querySelector('td:nth-child(7)').textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(subtotal);

    // Update data-subtotal checkbox jika dipilih
    const checkbox = row.querySelector('.checkbox_pilih');
    if (checkbox) {
        checkbox.dataset.subtotal = subtotal;
    }

    updateTotal();
}
function updateTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.checkbox_pilih:checked');
    checkboxes.forEach(cb => {
        total += parseFloat(cb.dataset.subtotal);
    });

    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2
    }).format(total);

    document.getElementById('total_display').textContent = formatted;
}
function showInfoPopup(nama, jenis, kategori, stok, harga, gambar) {
    const content = `
        <img src="../uploads/${gambar}" alt="${nama}" class="w-full max-h-64 object-contain rounded mb-4 shadow">
        <h3 class="text-xl font-semibold text-teal-700">${nama}</h3>
        <p><strong>Jenis:</strong> ${jenis}</p>
        <p><strong>Kategori:</strong> ${kategori}</p>
        <p><strong>Stok:</strong> ${stok}</p>
        <p class="text-lg font-semibold text-teal-600 mt-2">Harga: Rp ${harga}</p>
    `;
    document.getElementById('infoContent').innerHTML = content;
    document.getElementById('infoModal').classList.remove('hidden');
}

function closeInfoPopup() {
    document.getElementById('infoModal').classList.add('hidden');
    document.getElementById('infoContent').innerHTML = '';
}
</script>

</body>
</html>
