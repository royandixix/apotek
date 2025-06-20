<?php
include '../koneksi.php';

$tipe = $_GET['tipe'] ?? 'struk';

// Logika untuk ambil data transaksi & pembayaran
$query = "SELECT t.*, p.metode_pembayaran_222233, p.status_pembayaran_222233, p.tanggal_pembayaran_222233, p.jumlah_pembayaran_222233
          FROM transaksi_222233 t
          LEFT JOIN pembayaran_222233 p ON t.transaksi_id_222233 = p.transaksi_id_222233";

$result = $conn->query($query);

header("Content-Type: text/html");
echo "<h2 style='text-align:center'>Cetak Data Transaksi - " . strtoupper($tipe) . "</h2>";
echo "<table border='1' cellspacing='0' cellpadding='6' width='100%'>";
echo "<tr>
        <th>ID</th><th>Tanggal</th><th>Total</th><th>Status Transaksi</th>
        <th>Metode</th><th>Status Bayar</th><th>Tgl Bayar</th><th>Jumlah</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['transaksi_id_222233']}</td>";
    echo "<td>" . date('d-m-Y H:i', strtotime($row['tanggal_transaksi_222233'])) . "</td>";
    echo "<td>Rp " . number_format($row['total_harga_222233'], 0, ',', '.') . "</td>";
    echo "<td>{$row['status_transaksi_222233']}</td>";
    echo "<td>{$row['metode_pembayaran_222233']}</td>";
    echo "<td>{$row['status_pembayaran_222233']}</td>";
    echo "<td>" . ($row['tanggal_pembayaran_222233'] ? date('d-m-Y H:i', strtotime($row['tanggal_pembayaran_222233'])) : '-') . "</td>";
    echo "<td>Rp " . number_format($row['jumlah_pembayaran_222233'], 0, ',', '.') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<script>window.print();</script>";
?>
