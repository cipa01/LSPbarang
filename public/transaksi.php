<?php
session_start();
if (!isset($_SESSION['user_id'])) { // Menggunakan 'user_id' untuk memeriksa session
    header('Location: login.php');
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']); // Mendapatkan nama file saat ini
$db = require_once __DIR__ . '/../app/koneksi.php';

// Menambahkan query untuk mengambil data transaksi
$query = "SELECT t.id_transaksi, b.nama_barang, t.jumlah, t.total_harga, t.tanggal_transaksi 
          FROM transaksi t 
          LEFT JOIN barang b ON t.id_barang = b.id_barang";
$stmt = $db->prepare($query);
$stmt->execute();
$transaksi = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $total_harga = $_POST['total_harga'];

    // Siapkan query untuk mendapatkan id_barang
    $id_barang_query = "SELECT id_barang FROM barang WHERE nama_barang = :nama_barang";
    $id_barang_stmt = $db->prepare($id_barang_query);
    $id_barang_stmt->execute(['nama_barang' => $nama_barang]);
    $id_barang = $id_barang_stmt->fetchColumn();

    if ($id_barang === false) {
        // Tangani kasus di mana id_barang tidak ditemukan
        echo "Nama barang tidak ditemukan.";
        exit;
    }

    // Siapkan query untuk menambahkan transaksi
    $insertQuery = "INSERT INTO transaksi (id_user, id_barang, jumlah, total_harga, tanggal_transaksi) 
                     VALUES (:user_id, :id_barang, :jumlah, :total_harga, :tanggal_transaksi)";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        'user_id' => $_SESSION['user_id'],
        'id_barang' => $id_barang,
        'jumlah' => $jumlah,
        'total_harga' => $total_harga,
        'tanggal_transaksi' => $_POST['tanggal_transaksi'] // Menggunakan tanggal yang diinput oleh pengguna
    ]);

    // Redirect setelah menambahkan transaksi
    header('Location: transaksi.php');
    exit;
}

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
            <a href="dashboard.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="products.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg">
                <i class="fas fa-box mr-2"></i>Products
            </a>
            <a href="transaksi.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg <?php echo ($current_page == 'transaksi.php') ? 'bg-gray-700' : ''; ?>">
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

    <!-- Main Content -->
    <div class="ml-64 flex-1 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Transaksi Overview</h1>
            <a href="login.php" class="block text-black-700 py-3 px-6 hover:bg-gray-500 mt-auto rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
        <table class="min-w-full bg-white rounded-lg shadow-md mb-4"> <!-- Tambahkan mb-4 untuk margin bawah -->
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 border-b text-left">ID Transaksi</th>
                    <th class="p-3 border-b text-left">Nama Barang</th>
                    <th class="p-3 border-b text-left">Jumlah</th>
                    <th class="p-3 border-b text-left">Total Harga</th>
                    <th class="p-3 border-b text-left">Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $row): ?>
                    <tr>
                        <td class="p-3 border-b"><?php echo htmlspecialchars($row['id_transaksi']); ?></td>
                        <td class="p-3 border-b"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td class="p-3 border-b"><?php echo htmlspecialchars($row['jumlah']); ?></td>
                        <td class="p-3 border-b"><?php echo htmlspecialchars($row['total_harga']); ?></td>
                        <td class="p-3 border-b"><?php echo htmlspecialchars($row['tanggal_transaksi']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="flex justify-between items-center mb-6">
            <button id="addTransaksiBtn" class="bg-green-500 text-white rounded py-2 px-4">Add Transaksi</button>
        </div>
        <form id="transaksiForm" action="transaksi.php" method="POST" class="mb-6 hidden">
            <div class="flex flex-col mb-4">
                <label for="nama_barang" class="mb-2">Nama Barang</label>
                <select name="nama_barang" id="nama_barang" required class="border p-2 text-center">
                    <?php
                    // Menambahkan query untuk mengambil data barang
                    $barang_query = "SELECT nama_barang FROM barang";
                    $barang_stmt = $db->prepare($barang_query);
                    $barang_stmt->execute();
                    $barangs = $barang_stmt->fetchAll();

                    foreach ($barangs as $barang) {
                        echo '<option value="' . htmlspecialchars($barang['nama_barang']) . '">' . htmlspecialchars($barang['nama_barang']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="flex flex-col mb-4">
                <label for="jumlah" class="mb-2">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" required class="border p-2">
            </div>
            <div class="flex flex-col mb-4">
                <label for="alamat" class="mb-2">Total Harga</label>
                <input type="number" name="total_harga" id="total_harga" required class="border p-2">
            </div>
            <div class="flex flex-col mb-4">
                <label for="tanggal_transaksi" class="mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" required class="border p-2">
            </div>
            <button type="submit" class="bg-blue-500 text-white rounded py-2 px-4">Tambah Supplier</button>
        </form>
    </div>
    </div>
    <script>
        document.getElementById('addTransaksiBtn').addEventListener('click', function() {
            var form = document.getElementById('transaksiForm');
            form.classList.toggle('hidden');
        });
    </script>
</body>

</html>