<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../app/koneksi.php';

// Ambil filter bulan dan tahun dari request
$bulan = isset($_GET['bulan']) ? (int) $_GET['bulan'] : (int) date('m');
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

// Query mengambil data laporan berdasarkan bulan dan tahun
$query = "SELECT 
            COALESCE(l.id_laporan, '-') AS id_laporan,
            COALESCE(l.tgl_laporan, t.tanggal_transaksi) AS tgl_laporan,
            t.id_transaksi, 
            b.nama_barang, 
            k.nama_kategori, 
            t.jumlah, 
            t.total_harga, 
            t.tanggal_transaksi, 
            u.username AS petugas 
          FROM transaksi t 
          LEFT JOIN laporan l ON t.id_transaksi = l.id_transaksi 
          LEFT JOIN barang b ON t.id_barang = b.id_barang
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          LEFT JOIN user u ON t.id_user = u.id_user
          WHERE MONTH(t.tanggal_transaksi) = :bulan AND YEAR(t.tanggal_transaksi) = :tahun";

$stmt = $db->prepare($query);
$stmt->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging jika laporan masih kosong
if (empty($laporan)) {
    echo "<p style='color: red;'>Data laporan tidak ditemukan! Coba cek kembali transaksi.</p>";
    var_dump($laporan); // Debugging
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">
    <div class="w-64 h-screen bg-gray-800 fixed">
        <div class="flex items-center justify-center h-20 bg-gray-900">
            <h1 class="text-white text-2xl font-bold">Inventory System</h1>
        </div>
        <nav class="mt-4">
            <a href="dashboard.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg <?php echo ($current_page == 'dashboard.php') ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="products.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-box mr-2"></i>Products
            </a>
            <a href="transaksi.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-exchange-alt mr-2"></i>Transactions
            </a>
            <a href="suppliers.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-truck mr-2"></i>Suppliers
            </a>
            <a href="reports.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-chart-bar mr-2"></i>Reports
            </a>
            <a href="register2.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-user-plus mr-2"></i>Register User
            </a>
        </nav>
    </div>

    <div class="container mx-auto bg-white p-6 rounded-lg shadow ml-64 mt-8">
        <h1 class="text-2xl font-bold mb-4">Laporan Transaksi</h1>

        <form method="GET" class="mb-4">
            <label class="mr-2">Bulan:</label>
            <select name="bulan" class="border rounded p-2">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $bulan) ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                <?php endfor; ?>
            </select>
            <label class="ml-4 mr-2">Tahun:</label>
            <select name="tahun" class="border rounded p-2">
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo ($y == $tahun) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        </form>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">ID Laporan</th>
                    <th class="py-2 px-4 border">Tanggal Laporan</th>
                    <th class="py-2 px-4 border">ID Transaksi</th>
                    <th class="py-2 px-4 border">Nama Barang</th>
                    <th class="py-2 px-4 border">Kategori</th>
                    <th class="py-2 px-4 border">Jumlah</th>
                    <th class="py-2 px-4 border">Total Harga</th>
                    <th class="py-2 px-4 border">Tanggal Transaksi</th>
                    <th class="py-2 px-4 border">Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($laporan)): ?>
                    <?php foreach ($laporan as $row): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['id_laporan']); ?></td>
                            <td class="py-2 px-4 border"><?php echo date('d/m/Y', strtotime($row['tgl_laporan'])); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['id_transaksi']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['jumlah']); ?></td>
                            <td class="py-2 px-4 border">Rp <?php echo number_format($row['total_harga'], 2, ',', '.'); ?></td>
                            <td class="py-2 px-4 border"><?php echo date('d/m/Y', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($row['petugas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-red-500">Tidak ada data yang ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>