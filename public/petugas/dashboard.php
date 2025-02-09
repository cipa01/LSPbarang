<?php
session_start();
if (!isset($_SESSION['user_id'])) { // Menggunakan 'user_id' untuk memeriksa session
    header('Location: login.php');
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']); // Mendapatkan nama file saat ini
$db = require_once __DIR__ . '../../../app/koneksi.php';

// Get summary data
$totalBarang = $db->query("SELECT COUNT(*) as total FROM barang")->fetch(PDO::FETCH_ASSOC)['total'];
$totalSupplier = $db->query("SELECT COUNT(*) as total FROM supplier")->fetch(PDO::FETCH_ASSOC)['total'];
$totalTransaksi = $db->query("SELECT COUNT(*) as total FROM transaksi")->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex">
    <!-- Sidebar stays the same -->
    <div class="w-64 h-screen bg-gray-800 fixed">
        <div class="flex items-center justify-center h-20 bg-gray-900">
            <h1 class="text-white text-2xl font-bold">Inventory System</h1>
        </div>
        <nav class="mt-4">
            <a href="dashboard.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg <?php echo ($current_page == 'dashboard.php') ? 'bg-gray-700' : ''; ?>">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="../petugas/produkPetugas.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg ">
                <i class="fas fa-box mr-2"></i>Products
            </a>
            <a href="../petugas/transaksiPetugas.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-exchange-alt mr-2"></i>Transactions
            </a>
           
            <a href="../petugas/laporan.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-chart-bar mr-2"></i>Reports
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 flex-1 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Dashboard Overview</h1>
            <a href="log.php" class="block text-black-700 py-3 px-6 hover:bg-gray-500 mt-auto rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-500 rounded-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-75">Total Products</p>
                        <h3 class="text-2xl font-bold"><?php echo $totalBarang; ?></h3>
                    </div>
                    <i class="fas fa-box text-4xl opacity-75"></i>
                </div>
            </div>
            <div class="bg-green-500 rounded-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-75">Total Suppliers</p>
                        <h3 class="text-2xl font-bold"><?php echo $totalSupplier; ?></h3>
                    </div>
                    <i class="fas fa-truck text-4xl opacity-75"></i>
                </div>
            </div>
            <div class="bg-red-500 rounded-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-75">Total Transactions</p>
                        <h3 class="text-2xl font-bold"><?php echo $totalTransaksi; ?></h3>
                    </div>
                    <i class="fas fa-exchange-alt text-4xl opacity-75"></i>
                </div>
            </div>
        </div>

        <!-- Recent Products Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold">Recent Products</h2>
                <a href="products.php" class="text-blue-500 hover:underline">View All</a>
            </div>
            <div class="p-4">
                <table class="w-full">
                    <thead>
                        <tr class="text-left bg-gray-50">
                            <th class="p-3 border-b">ID</th>
                            <th class="p-3 border-b">Product Name</th>
                            <th class="p-3 border-b">Price</th>
                            <th class="p-3 border-b">Stock</th>
                            <th class="p-3 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM barang ORDER BY id_barang DESC LIMIT 5");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $stockStatus = $row['stok'] > 10 ?
                                '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">In Stock</span>' :
                                '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Low Stock</span>';

                            echo "<tr class='hover:bg-gray-50'>
                                    <td class='p-3 border-b'>{$row['id_barang']}</td>
                                    <td class='p-3 border-b'>{$row['nama_barang']}</td>
                                    <td class='p-3 border-b'>Rp " . number_format($row['harga'], 2, ',', '.') . "</td>
                                    <td class='p-3 border-b'>{$row['stok']}</td>
                                    <td class='p-3 border-b'>$stockStatus</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>