<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

// Cek apakah sudah login dan apakah pengguna adalah pembeli
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../login.php");
    exit();
}

$message = '';

// Ambil data pengguna pembeli
$user_id = $_SESSION['user_id'];
$query = "SELECT u.username_222233, u.email_222233, u.nama_222233, p.alamat_222233 
          FROM users_222233 u
          JOIN pembeli_222233 p ON u.user_id_222233 = p.user_id_222233
          WHERE u.user_id_222233 = $user_id";
$result = mysqli_query($conn, $query);
$pembeli_data = mysqli_fetch_assoc($result);

// Proses Update Profil
if (isset($_POST['update_profil'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Ambil password lama dari database
    $check_password_query = "SELECT password_222233 FROM users_222233 WHERE user_id_222233 = $user_id";
    $check_result = mysqli_query($conn, $check_password_query);
    $user_data = mysqli_fetch_assoc($check_result);

    if (password_verify($current_password, $user_data['password_222233'])) {
        // Update data user
        $update_query = "UPDATE users_222233 
                         SET username_222233 = '$username', email_222233 = '$email', nama_222233 = '$nama'
                         WHERE user_id_222233 = $user_id";
        mysqli_query($conn, $update_query);

        // Jika password baru diisi
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_query = "UPDATE users_222233 
                                      SET password_222233 = '$hashed_password' 
                                      WHERE user_id_222233 = $user_id";
            mysqli_query($conn, $update_password_query);
        }

        // Update alamat pembeli
        $update_pembeli_query = "UPDATE pembeli_222233 
                                 SET alamat_222233 = '$alamat' 
                                 WHERE user_id_222233 = $user_id";
        mysqli_query($conn, $update_pembeli_query);

        $message = 'Profil berhasil diperbarui!';
    } else {
        $message = 'Password lama salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-teal-700 mb-6">Profil Saya</h1>

        <?php if ($message): ?>
            <div class="mb-4 p-4 bg-blue-100 text-blue-800 rounded-lg">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($pembeli_data): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-teal-700 mb-4">Edit Profil</h2>
                <form action="profil.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700">Username:</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($pembeli_data['username_222233']) ?>" required class="w-full p-3 border rounded-md mt-2">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($pembeli_data['email_222233']) ?>" required class="w-full p-3 border rounded-md mt-2">
                    </div>
<!-- 
                    <div class="mb-4">
                        <label for="nama" class="block text-gray-700">Nama Lengkap:</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($pembeli_data['nama_222233']) ?>" required class="w-full p-3 border rounded-md mt-2">
                    </div> -->

                    <div class="mb-4">
                        <label for="alamat" class="block text-gray-700">Alamat:</label>
                        <textarea id="alamat" name="alamat" required class="w-full p-3 border rounded-md mt-2"><?= htmlspecialchars($pembeli_data['alamat_222233']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="current_password" class="block text-gray-700">Password Lama (untuk konfirmasi perubahan):</label>
                        <input type="password" id="current_password" name="current_password" required class="w-full p-3 border rounded-md mt-2">
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700">Password Baru (kosongkan jika tidak ingin mengubah):</label>
                        <input type="password" id="new_password" name="new_password" class="w-full p-3 border rounded-md mt-2">
                    </div>

                    <div>
                        <input type="submit" name="update_profil" value="Perbarui Profil" class="bg-teal-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-teal-700">
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
