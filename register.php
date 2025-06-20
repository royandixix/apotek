<?php
session_start();
include 'koneksi.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $nama = '';

    // Ambil nama dan data tambahan sesuai role
    switch ($role) {
        case 'admin':
            $nama = $_POST['nama_admin'];
            break;
        case 'kasir':
            $nama = $_POST['nama_kasir'];
            break;
        case 'pembeli':
            $nama = $_POST['nama_pembeli'];
            $alamat_pembeli = $_POST['alamat'];
            break;
        case 'supplier':
            $nama = $_POST['nama_supplier'];
            $nama_perusahaan = $_POST['nama_perusahaan'];
            $alamat_supplier = $_POST['alamat'];
            $no_telp = $_POST['no_telp'];
            break;
    }

    // Insert ke tabel users
    $stmt = $conn->prepare("INSERT INTO users_222233 (nama_222233, username_222233, email_222233, password_222233, role_222233) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $username, $email, $password, $role);
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Insert ke tabel peran
        switch ($role) {
            case 'admin':
                $conn->query("INSERT INTO admin_222233 (user_id_222233) VALUES ($user_id)");
                break;
            case 'kasir':
                $conn->query("INSERT INTO kasir_222233 (user_id_222233) VALUES ($user_id)");
                break;
            case 'pembeli':
                $stmt = $conn->prepare("INSERT INTO pembeli_222233 (user_id_222233, alamat_222233) VALUES (?, ?)");
                $stmt->bind_param("is", $user_id, $alamat_pembeli);
                $stmt->execute();
                break;
            case 'supplier':
                $stmt = $conn->prepare("INSERT INTO supplier_222233 (user_id_222233, nama_perusahaan_222233, alamat_222233, no_telp_222233) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $nama_perusahaan, $alamat_supplier, $no_telp);
                $stmt->execute();
                break;
        }

        header("Location: login.php?success=1");
        exit;
    } else {
        $message = "<p class='text-red-600'>Registrasi gagal. Username atau email mungkin sudah terdaftar.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-teal-50 min-h-screen flex items-center justify-center">
<div class="flex bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-4xl border-t-8 border-teal-500 mx-auto my-12">
    <!-- Gambar Apotek -->
    <div class="hidden md:block md:w-1/2">
        <img src="apotik.jpg" alt="Gambar Apotek" class="h-full w-full object-cover" />
    </div>

    <!-- Form Registrasi -->
    <div class="w-full md:w-1/2 p-8">
        <h1 class="text-2xl font-bold text-teal-600 text-center mb-6">Registrasi Pengguna Apotek</h1>
        <?php if (!empty($message)) echo $message; ?>
        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required class="w-full border rounded p-2">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="w-full border rounded p-2">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="w-full border rounded p-2">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Daftar Sebagai</label>
                <select id="role" name="role" onchange="toggleRoleFields()" required class="w-full border rounded p-2">
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    <option value="supplier">Supplier</option>
                    <option value="pembeli">Pembeli</option>
                </select>
            </div>

            <!-- Admin -->
            <div id="admin-fields" class="hidden">
                <label class="block text-sm mt-2 font-medium text-gray-700">Nama Admin</label>
                <input type="text" name="nama_admin" class="w-full border rounded p-2">
            </div>

            <!-- Kasir -->
            <div id="kasir-fields" class="hidden">
                <label class="block text-sm mt-2 font-medium text-gray-700">Nama Kasir</label>
                <input type="text" name="nama_kasir" class="w-full border rounded p-2">
            </div>

            <!-- Supplier -->
            <div id="supplier-fields" class="hidden space-y-2">
                <label class="block text-sm font-medium text-gray-700">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="w-full border rounded p-2">
                <label class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="w-full border rounded p-2">
                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" class="w-full border rounded p-2"></textarea>
                <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                <input type="text" name="no_telp" class="w-full border rounded p-2">
            </div>

            <!-- Pembeli -->
            <div id="pembeli-fields" class="hidden space-y-2">
                <label class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
                <input type="text" name="nama_pembeli" class="w-full border rounded p-2">
                <label class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" class="w-full border rounded p-2"></textarea>
            </div>

            <div>
                <input type="submit" value="Daftar" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 rounded">
            </div>
        </form>
        <p class="text-center text-sm text-gray-600 mt-4">
            Sudah punya akun? <a href="login.php" class="text-teal-600 hover:underline">Login di sini</a>
        </p>
    </div>
</div>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById("role").value;
            const fields = ['admin', 'kasir', 'supplier', 'pembeli'];
            fields.forEach(f => {
                document.getElementById(f + '-fields').classList.add('hidden');
            });
            if (fields.includes(role)) {
                document.getElementById(role + '-fields').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
