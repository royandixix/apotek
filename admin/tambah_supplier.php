<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses jika form dit-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dari form
    $nama_supplier = $_POST['nama_supplier'];
    $nama_perusahaan = $_POST['nama_perusahaan'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi form (misalnya, tidak boleh kosong)
    if (empty($nama_supplier) || empty($nama_perusahaan) || empty($alamat) || empty($no_telp) || empty($email) || empty($username) || empty($password)) {
        $error = "Semua field harus diisi!";
    } else {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data ke tabel users_222233
        $sql_user = "INSERT INTO users_222233 (nama_222233, username_222233, email_222233, password_222233, role_222233) 
                     VALUES (?, ?, ?, ?, 'supplier')";
        
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ssss", $nama_supplier, $username, $email, $hashed_password);

        if ($stmt_user->execute()) {
            // Ambil user_id yang baru saja dimasukkan
            $user_id = $stmt_user->insert_id;

            // Insert data ke tabel supplier_222233
            $sql_supplier = "INSERT INTO supplier_222233 (user_id_222233, nama_perusahaan_222233, alamat_222233, no_telp_222233) 
                             VALUES (?, ?, ?, ?)";
            
            $stmt_supplier = $conn->prepare($sql_supplier);
            $stmt_supplier->bind_param("isss", $user_id, $nama_perusahaan, $alamat, $no_telp);

            if ($stmt_supplier->execute()) {
                header("Location: supplier.php?message=success");
                exit;
            } else {
                $error = "Gagal menambahkan data supplier.";
            }
        } else {
            $error = "Gagal menambahkan data pengguna.";
        }

        // Tutup statement
        $stmt_user->close();
        $stmt_supplier->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Supplier - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Supplier</h1>

    <!-- Menampilkan pesan error jika ada -->
    <?php if (isset($error)): ?>
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Form untuk tambah supplier -->
    <form action="tambah_supplier.php" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        
        <div>
            <label for="nama_supplier" class="block text-sm font-medium text-gray-700">Nama Supplier</label>
            <input type="text" id="nama_supplier" name="nama_supplier" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <div>
            <label for="nama_perusahaan" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>
        
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>
        
        <div>
            <label for="no_telp" class="block text-sm font-medium text-gray-700">No. Telepon</label>
            <input type="text" id="no_telp" name="no_telp" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
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
