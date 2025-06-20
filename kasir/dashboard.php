<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil kasir_id dari tabel kasir
$kasir_id = null;
$stmt = $conn->prepare("SELECT kasir_id_222233 FROM kasir_222233 WHERE user_id_222233 = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $kasir_id = $row['kasir_id_222233'];
} else {
    die("❌ Kasir tidak ditemukan.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<?php include 'sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold text-teal-700 mb-4">Dashboard Kasir</h1>
    <p class="mb-6 text-gray-700">
        Selamat datang, <strong><?= htmlspecialchars($username) ?></strong>!
        Anda login sebagai <strong>Kasir</strong>
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php
    $statuses = [
        'pending' => ['label' => 'Transaksi Pending', 'bg' => 'blue'],
        'sukses'  => ['label' => 'Transaksi Sukses',  'bg' => 'green'],
        'batal'   => ['label' => 'Transaksi Batal',   'bg' => 'red'],
    ];

    foreach ($statuses as $status => $info) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total 
            FROM transaksi_222233 
            WHERE status_transaksi_222233 = ? 
              AND (kasir_id_222233 IS NULL OR kasir_id_222233 = ?)
        ");
        $stmt->bind_param("si", $status, $kasir_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        $total = isset($data['total']) ? $data['total'] : 0;
    ?>
        <div class="bg-<?= $info['bg'] ?>-100 p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold text-<?= $info['bg'] ?>-700 mb-2"><?= $info['label'] ?></h2>
            <p class="text-4xl font-bold text-<?= $info['bg'] ?>-800"><?= $total ?></p>
            <a href="transaksi_pembelian.php" class="inline-block mt-4 bg-<?= $info['bg'] ?>-600 text-white px-4 py-2 rounded hover:bg-<?= $info['bg'] ?>-700">
                Lihat Detail
            </a>
        </div>
    <?php } ?>
    </div>
</div>

</body>
</html>
