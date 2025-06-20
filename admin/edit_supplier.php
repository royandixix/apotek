<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil ID supplier dari URL
if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];

    // Ambil data supplier dan user terkait berdasarkan supplier_id
    $sql = "SELECT s.*, u.username_222233, u.nama_222233, u.email_222233 
            FROM supplier_222233 s 
            JOIN users_222233 u ON s.user_id_222233 = u.user_id_222233
            WHERE s.supplier_id_222233 = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();

    // Jika data tidak ditemukan
    if (!$supplier) {
        die("Supplier tidak ditemukan.");
    }
}

// Proses jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_supplier = $_POST['nama_supplier'];  // Ambil nama yang diedit
    $username = $_POST['username'];            // Username yang diubah
    $email = $_POST['email'];                  // Email yang diubah
    $password = $_POST['password'];            // Password yang mungkin diubah
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    // Validasi form (pastikan tidak ada yang kosong)
    if (empty($nama_supplier) || empty($username) || empty($email) || empty($nama_perusahaan) || empty($alamat) || empty($no_telp)) {
        $error = "Semua field harus diisi!";
    } else {
        // Jika password diubah, hash password baru
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hash password baru
            // Query untuk update user dengan password baru
            $sql_user = "UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ?, password_222233 = ? WHERE user_id_222233 = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("ssssi", $nama_supplier, $username, $email, $hashed_password, $supplier['user_id_222233']);
        } else {
            // Update tanpa password jika tidak ada perubahan password
            $sql_user = "UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ? WHERE user_id_222233 = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("sssi", $nama_supplier, $username, $email, $supplier['user_id_222233']);
        }

        // Update data supplier di tabel supplier_222233
        $sql_supplier = "UPDATE supplier_222233 SET nama_perusahaan_222233 = ?, alamat_222233 = ?, no_telp_222233 = ? WHERE supplier_id_222233 = ?";
        $stmt_supplier = $conn->prepare($sql_supplier);
        $stmt_supplier->bind_param("sssi", $nama_perusahaan, $alamat, $no_telp, $supplier_id);

        // Eksekusi query untuk update user dan supplier
        if ($stmt_user->execute() && $stmt_supplier->execute()) {
            header("Location: supplier.php?message=success");
            exit;
        } else {
            $error = "Gagal memperbarui data supplier.";
        }

        $stmt_user->close();
        $stmt_supplier->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Supplier - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Supplier</h1>

    <!-- Menampilkan pesan error jika ada -->
    <?php if (isset($error)): ?>
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

<form action="edit_supplier.php?id=<?php echo $supplier_id; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
    <!-- Nama Supplier (nama di users_222233) -->
    <div>
        <label for="nama_supplier" class="block text-sm font-medium text-gray-700">Nama Supplier</label>
        <input type="text" id="nama_supplier" name="nama_supplier" value="<?php echo htmlspecialchars($supplier['nama_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>
    
    <!-- Username -->
    <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($supplier['username_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>

    <!-- Password (optional) -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin mengubah)</label>
        <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
    </div>

    <!-- Nama Perusahaan -->
    <div>
        <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
        <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo htmlspecialchars($supplier['nama_perusahaan_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>
    
    <!-- Alamat -->
    <div>
        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
        <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($supplier['alamat_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>
    
    <!-- No Telepon -->
    <div>
        <label for="no_telp" class="block text-sm font-medium text-gray-700">No. Telepon</label>
        <input type="text" id="no_telp" name="no_telp" value="<?php echo htmlspecialchars($supplier['no_telp_222233']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
    </div>

    <div class="flex justify-end space-x-4">
        <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
            Simpan
        </button>
        <a href="supplier.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
            Batal
        </a>
    </div>
</form>

</div>

</body>
</html>
