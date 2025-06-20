<?php
session_start();
include '../koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: pembeli.php");
    exit;
}

$pembeli_id = intval($_GET['id']);

// Ambil data user dan pembeli berdasarkan pembeli_id
$sql_user = "SELECT u.user_id_222233, u.nama_222233, u.username_222233, u.email_222233, p.alamat_222233 
             FROM users_222233 u 
             JOIN pembeli_222233 p ON u.user_id_222233 = p.user_id_222233 
             WHERE p.pembeli_id_222233 = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $pembeli_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    header("Location: pembeli.php");
    exit;
}

$row_user = $result_user->fetch_assoc();
$user_id = $row_user['user_id_222233'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pembeli = trim($_POST['nama_pembeli']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $alamat = trim($_POST['alamat']);

    if (empty($nama_pembeli) || empty($username) || empty($email) || empty($alamat)) {
        $error = "Semua field harus diisi!";
    } else {
        // Validasi username jika berubah
        if ($username !== $row_user['username_222233']) {
            $stmt_check = $conn->prepare("SELECT user_id_222233 FROM users_222233 WHERE username_222233 = ? AND user_id_222233 != ?");
            $stmt_check->bind_param("si", $username, $user_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $error = "Username sudah digunakan oleh pengguna lain!";
            }
            $stmt_check->close();
        }

        if (!isset($error)) {
            // Siapkan SQL dan bind parameter
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_update_user = "UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ?, password_222233 = ? WHERE user_id_222233 = ?";
                $stmt_user_update = $conn->prepare($sql_update_user);
                $stmt_user_update->bind_param("ssssi", $nama_pembeli, $username, $email, $hashed_password, $user_id);
            } else {
                $sql_update_user = "UPDATE users_222233 SET nama_222233 = ?, username_222233 = ?, email_222233 = ? WHERE user_id_222233 = ?";
                $stmt_user_update = $conn->prepare($sql_update_user);
                $stmt_user_update->bind_param("sssi", $nama_pembeli, $username, $email, $user_id);
            }

            if ($stmt_user_update->execute()) {
                $stmt_alamat = $conn->prepare("UPDATE pembeli_222233 SET alamat_222233 = ? WHERE user_id_222233 = ?");
                $stmt_alamat->bind_param("si", $alamat, $user_id);

                if ($stmt_alamat->execute()) {
                    header("Location: pembeli.php?message=success");
                    exit;
                } else {
                    $error = "Gagal memperbarui alamat pembeli.";
                }
                $stmt_alamat->close();
            } else {
                $error = "Gagal memperbarui data pengguna.";
            }

            $stmt_user_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pembeli - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-6">Edit Pembeli</h1>

    <!-- Menampilkan pesan error jika ada -->
    <?php if (isset($error)): ?>
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="edit_pembeli.php?id=<?php echo $user_id; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        <!-- Nama Pembeli -->
        <div>
            <label for="nama_pembeli" class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
            <input type="text" id="nama_pembeli" name="nama_pembeli" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" value="<?php echo htmlspecialchars($row_user['nama_222233']); ?>" required>
        </div>

        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" value="<?php echo htmlspecialchars($row_user['username_222233']); ?>" required>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" value="<?php echo htmlspecialchars($row_user['email_222233']); ?>" required>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <!-- Alamat -->
        <div>
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea id="alamat" name="alamat" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500" required><?php echo htmlspecialchars($row_user['alamat_222233']); ?></textarea>
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