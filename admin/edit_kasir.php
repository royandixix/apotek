<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil ID kasir dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: kasir.php");
    exit;
}

$kasir_id = $_GET['id'];
$error_message = '';

// Ambil data kasir dan user
$sql = "SELECT k.kasir_id_222233, u.user_id_222233, u.nama_222233, u.username_222233, u.email_222233 
        FROM kasir_222233 k 
        JOIN users_222233 u ON k.user_id_222233 = u.user_id_222233 
        WHERE k.kasir_id_222233 = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kasir_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: kasir.php");
    exit;
}

$kasir = $result->fetch_assoc();

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek duplikat username/email (kecuali miliknya sendiri)
    $check = $conn->prepare("SELECT * FROM users_222233 WHERE (username_222233 = ? OR email_222233 = ?) AND user_id_222233 != ?");
    $check->bind_param("ssi", $username, $email, $kasir['user_id_222233']);
    $check->execute();
    $duplicate = $check->get_result();

    if ($duplicate->num_rows > 0) {
        $error_message = "Username atau email sudah digunakan.";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ?, password_222233 = ? WHERE user_id_222233 = ?");
            $update->bind_param("ssssi", $nama, $username, $email, $hashed_password, $kasir['user_id_222233']);
        } else {
            $update = $conn->prepare("UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ? WHERE user_id_222233 = ?");
            $update->bind_param("sssi", $nama, $username, $email, $kasir['user_id_222233']);
        }

        if ($update->execute()) {
            header("Location: kasir.php");
            exit;
        } else {
            $error_message = "Gagal mengupdate data kasir.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kasir - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Kasir</h1>

    <?php if ($error_message): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="bg-white p-6 rounded-xl shadow-md space-y-4">
        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($kasir['nama_222233']) ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($kasir['username_222233']) ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($kasir['email_222233']) ?>" required class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" id="password" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg">
        </div>

        <div class="flex justify-end space-x-4">
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow-md">
                Simpan Perubahan
            </button>
            <a href="kasir.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Batal
            </a>
        </div>
    </form>
</div>

</body>
</html>
