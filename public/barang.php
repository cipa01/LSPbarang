<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = require_once __DIR__ . '/../app/koneksi.php';
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data kategori dan supplier dari database
$kategori_query = $db->query("SELECT id_kategori, nama_kategori FROM kategori");
$supplier_query = $db->query("SELECT id_supplier, nama_supplier FROM supplier");
$kategoris = $kategori_query->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $supplier_query->fetchAll(PDO::FETCH_ASSOC);

// Ambil data barang dari database
$barang_query = $db->query("SELECT b.id_barang, b.nama_barang, k.nama_kategori, s.nama_supplier, b.harga, b.stok 
                              FROM barang b 
                              JOIN kategori k ON b.id_kategori = k.id_kategori 
                              JOIN supplier s ON b.id_supplier = s.id_supplier");
$barangs = $barang_query->fetchAll(PDO::FETCH_ASSOC);

// Proses input barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_supplier = $_POST['id_supplier'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Validasi input
    if (empty($nama_barang) || empty($id_kategori) || empty($id_supplier) || empty($harga) || empty($stok)) {
        die('Semua field harus diisi.');
    }

    // Siapkan dan eksekusi query untuk memasukkan data barang
    $stmt = $db->prepare("INSERT INTO barang (nama_barang, id_kategori, id_supplier, harga, stok) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nama_barang, $id_kategori, $id_supplier, $harga, $stok])) {
        header('Location: barang.php?success=1');
        exit;
    } else {
        die('Gagal menambahkan barang.');
    }
}

// Proses hapus barang
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];

    // Siapkan dan eksekusi query untuk menghapus data barang
    $stmt = $db->prepare("DELETE FROM barang WHERE id_barang = ?");
    if ($stmt->execute([$id_barang])) {
        header('Location: barang.php?deleted=1');
        exit;
    } else {
        die('Gagal menghapus barang.');
    }
}

// Proses edit barang
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_supplier = $_POST['id_supplier'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Validasi input
    if (empty($nama_barang) || empty($id_kategori) || empty($id_supplier) || empty($harga) || empty($stok)) {
        die('Semua field harus diisi.');
    }

    // Siapkan dan eksekusi query untuk memperbarui data barang
    $stmt = $db->prepare("UPDATE barang SET nama_barang = ?, id_kategori = ?, id_supplier = ?, harga = ?, stok = ? WHERE id_barang = ?");
    if ($stmt->execute([$nama_barang, $id_kategori, $id_supplier, $harga, $stok, $id_barang])) {
        header('Location: barang.php?updated=1');
        exit;
    } else {
        die('Gagal memperbarui barang.');
    }
}

// Ambil data barang untuk edit
$barang_to_edit = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];
    $stmt = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $stmt->execute([$id_barang]);
    $barang_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Manajemen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
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

    <div class="ml-64 flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Barang Overview</h1>
            <a href="login.php" class="block text-black-700 py-3 px-6 hover:bg-gray-500 mt-auto rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>

        <nav class="mt-2 flex space-x-2 "> <!-- Menambahkan mb-4 untuk margin bawah -->
            <button onclick="location.href='products.php' " class="bg-gray-300 py-2 px-3 hover:bg-gray-400 text-lg rounded mb-4">
                <i class="fas fa-list mr-2"></i>Kategori
            </button>
            <button onclick="location.href='barang.php'" class="bg-gray-300 py-2 px-4 hover:bg-gray-400 text-lg rounded mb-4 <?php echo ($current_page == 'barang.php') ? 'bg-gray-500' : ''; ?>">
                <i class="fas fa-box mr-2"></i>Barang
            </button>
        </nav>
        <button id="toggleForm" class="bg-blue-500 text-white py-2 px-4 rounded mb-6" onclick="toggleForm()">Add Data</button>
        <form id="dataForm" action="barang.php" method="POST" class="mb-6 hidden">
            <input type="hidden" name="action" value="<?= $barang_to_edit ? 'edit' : 'add' ?>">
            <?php if ($barang_to_edit): ?>
                <input type="hidden" name="id_barang" value="<?= $barang_to_edit['id_barang'] ?>">
            <?php endif; ?>
            <div class="mb-4">
                <label for="nama_barang" class="block text-gray-700">Nama Barang:</label>
                <input type="text" name="nama_barang" id="nama_barang" value="<?= $barang_to_edit['nama_barang'] ?? '' ?>" required class="border rounded p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="id_kategori" class="block text-gray-700">Kategori:</label>
                <select name="id_kategori" id="id_kategori" required class="border rounded p-2 w-full">
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategoris as $kategori): ?>
                        <option value="<?= $kategori['id_kategori'] ?>" <?= (isset($barang_to_edit) && $barang_to_edit['id_kategori'] == $kategori['id_kategori']) ? 'selected' : '' ?>><?= $kategori['nama_kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="id_supplier" class="block text-gray-700">Supplier:</label>
                <select name="id_supplier" id="id_supplier" required class="border rounded p-2 w-full">
                    <option value="">Pilih Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['id_supplier'] ?>" <?= (isset($barang_to_edit) && $barang_to_edit['id_supplier'] == $supplier['id_supplier']) ? 'selected' : '' ?>><?= $supplier['nama_supplier'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="harga" class="block text-gray-700">Harga:</label>
                <input type="number" name="harga" id="harga" value="<?= $barang_to_edit['harga'] ?? '' ?>" required class="border rounded p-2 w-full" step="0.01">
            </div>
            <div class="mb-4">
                <label for="stok" class="block text-gray-700">Stok:</label>
                <input type="number" name="stok" id="stok" value="<?= $barang_to_edit['stok'] ?? '' ?>" required class="border rounded p-2 w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded" onclick="return confirmUpdate();"><?= $barang_to_edit ? 'Update Barang' : 'Input Barang' ?></button>
            <button type="button" class="bg-red-500 text-white py-2 px-4 rounded ml-2" onclick="closeEdit();">Close</button>
        </form>

        <table class="min-w-full bg-white rounded-lg shadow-md mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 border-b text-left">ID</th>
                    <th class="p-3 border-b text-left">Nama Barang</th>
                    <th class="p-3 border-b text-left">Kategori</th>
                    <th class="p-3 border-b text-left">Supplier</th>
                    <th class="p-3 border-b text-left">Harga</th>
                    <th class="p-3 border-b text-left">Stok</th>
                    <th class="p-3 border-b text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barangs as $barang): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= $barang['id_barang'] ?></td>
                        <td class="border px-4 py-2"><?= $barang['nama_barang'] ?></td>
                        <td class="border px-4 py-2"><?= $barang['nama_kategori'] ?></td>
                        <td class="border px-4 py-2"><?= $barang['nama_supplier'] ?></td>
                        <td class="border px-4 py-2"><?= number_format($barang['harga'], 2) ?></td>
                        <td class="border px-4 py-2"><?= $barang['stok'] ?></td>
                        <td class="border px-4 py-2">
                            <a href="barang.php?action=edit&id_barang=<?= $barang['id_barang'] ?>" class="text-blue-500">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="barang.php?action=delete&id_barang=<?= $barang['id_barang'] ?>" class="text-red-500 ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleForm() {
            var form = document.getElementById('dataForm');
            form.classList.toggle('hidden');
        }

        // Tambahkan logika untuk menampilkan form jika ada barang yang diedit
        <?php if ($barang_to_edit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('dataForm').classList.remove('hidden');
            });
        <?php endif; ?>

        function confirmUpdate() {
            return confirm('Apakah Anda yakin ingin memperbarui barang ini?');
        }

        function closeEdit() {
            document.getElementById('dataForm').classList.add('hidden');
            resetForm(); // Reset form fields when closing
        }

        function resetForm() {
            // Reset form fields
            document.getElementById('dataForm').reset();
            // Clear any selected options in the dropdowns
            document.getElementById('id_kategori').selectedIndex = 0;
            document.getElementById('id_supplier').selectedIndex = 0;

            // Clear text inputs
            document.getElementById('nama_barang').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('stok').value = '';
        }
    </script>
</body>

</html>