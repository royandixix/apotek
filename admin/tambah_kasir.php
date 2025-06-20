<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // plaintext
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Validasi: username & email harus unik
    $check = $conn->prepare("SELECT * FROM users_222233 WHERE username_222233 = ? OR email_222233 = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Username atau email sudah digunakan.";
    } else {
        // Masukkan ke tabel users_222233
        $stmt = $conn->prepare("INSERT INTO users_222233 (nama_222233, username_222233, email_222233, password_222233, role_222233) VALUES (?, ?, ?, ?, 'kasir')");
        $stmt->bind_param("ssss", $nama, $username, $email, $hashed_password);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Masukkan ke tabel kasir_222233
            $stmt2 = $conn->prepare("INSERT INTO kasir_222233 (user_id_222233) VALUES (?)");
            $stmt2->bind_param("i", $user_id);
            if ($stmt2->execute()) {
                header("Location: kasir.php");
                exit;
            } else {
                $error_message = "Gagal menyimpan ke tabel kasir.";
            }
        } else {
            $error_message = "Gagal menyimpan ke tabel user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kasir - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Tambah Kasir Baru</h1>

    <?php if ($error_message): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <!-- Form Tambah Kasir -->
    <form method="POST" action="" class="bg-white p-6 rounded-xl shadow-md space-y-4">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div class="flex justify-end space-x-4">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                Simpan
            </button>
            <a href="kasir.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
</div>

</body>
</html>
