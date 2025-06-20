<?php
session_start();
include 'koneksi.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users_222233 WHERE username_222233 = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_222233'])) {
            $_SESSION['user_id'] = $user['user_id_222233'];
            $_SESSION['role'] = $user['role_222233'];
            $_SESSION['username'] = $user['username_222233'];

            switch ($user['role_222233']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'kasir':
                    header("Location: kasir/dashboard.php");
                    break;
                case 'pembeli':
                    header("Location: pembeli/dashboard.php");
                    break;
                case 'supplier':
                    header("Location: supplier/dashboard.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit;
        } else {
            $message = "<p class='text-red-600 text-sm mt-2'>Password salah.</p>";
        }
    } else {
        $message = "<p class='text-red-600 text-sm mt-2'>Username tidak ditemukan.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Apotek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="flex bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-4xl border-t-8 border-teal-500">
        <!-- Bagian Gambar -->
        <div class="hidden md:block md:w-1/2">
            <img src="apotik.jpg" alt="Gambar Apotek" class="h-full w-full object-cover" />
        </div>

        <!-- Form Login -->
        <div class="w-full md:w-1/2 p-8">
            <h1 class="text-2xl font-bold text-center text-teal-600 mb-6">Sistem Penjualan Obat Apotek</h1>
            <h2 class="text-xl font-semibold mb-4 text-center text-gray-700">Login</h2>
            <?php echo $message; ?>
            <form action="login.php" method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-teal-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400"/>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-3 py-2 border border-teal-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-400"/>
                </div>
                <div>
                    <input type="submit" value="Login" class="w-full bg-teal-500 text-white py-2 rounded-md hover:bg-teal-600 transition duration-200 cursor-pointer"/>
                </div>
            </form>
            <p class="text-center text-sm text-gray-600 mt-4">
                Belum punya akun? <a href="register.php" class="text-teal-600 hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</body>

</html>
