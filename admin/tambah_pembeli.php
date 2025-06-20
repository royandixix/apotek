<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Proses jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pembeli = $_POST['nama_pembeli'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];

    // Validasi form
    if (empty($nama_pembeli) || empty($username) || empty($email) || empty($password) || empty($alamat)) {
        $error = "Semua field harus diisi!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data ke tabel users_222233
        $sql_user = "INSERT INTO users_222233 (nama_222233, username_222233, email_222233, password_222233, role_222233) VALUES (?, ?, ?, ?, 'pembeli')";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ssss", $nama_pembeli, $username, $email, $hashed_password);

        if ($stmt_user->execute()) {
            $user_id = $stmt_user->insert_id;  // Ambil user_id yang baru dimasukkan

            // Insert data ke tabel pembeli_222233
            $sql_pembeli = "INSERT INTO pembeli_222233 (user_id_222233, alamat_222233) VALUES (?, ?)";
            $stmt_pembeli = $conn->prepare($sql_pembeli);
            $stmt_pembeli->bind_param("is", $user_id, $alamat);

            if ($stmt_pembeli->execute()) {
                header("Location: pembeli.php?message=success");
                exit;
            } else {
                $error = "Gagal menambahkan data pembeli.";
            }

            $stmt_pembeli->close();
        } else {
            $error = "Gagal menambahkan user.";
        }

        $stmt_user->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pembeli - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Pembeli</h1>

    <!-- Menampilkan pesan error jika ada -->
    <?php if (isset($error)): ?>
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Form untuk tambah pembeli -->
    <form action="tambah_pembeli.php" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        <!-- Nama Pembeli -->
        <div>
            <label for="nama_pembeli" class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
            <input type="text" id="nama_pembeli" name="nama_pembeli" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>
        
        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required>
        </div>

        <!-- Alamat -->
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea id="alamat" name="alamat" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required></textarea>
        </div>

        <div class="flex justify-end space-x-4">
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                Simpan
            </button>
            <a href="pembeli.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
</div>

</body>
</html>
