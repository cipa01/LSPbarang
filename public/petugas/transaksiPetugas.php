<?php
session_start();
if (!isset($_SESSION['user_id'])) { // Menggunakan 'user_id' untuk memeriksa session
    header('Location: login.php');
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']); // Mendapatkan nama file saat ini
$db = require_once __DIR__ . '../../../app/koneksi.php';


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
            <a href="../petugas/produkPetugas.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg ">
                <i class="fas fa-box mr-2"></i>Products
            </a>
            <a href="../petugas/transaksiPetugas.php" class="block text-gray-300 py-4 px-6 hover:bg-gray-700 text-lg  <?php echo ($current_page == 'transaksiPetugas.php') ? 'bg-gray-700' : ''; ?>">
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
            <h1 class="text-2xl font-bold">Transactions Overview</h1>
            <a href="../../public/login" class="block text-black-700 py-3 px-6 hover:bg-gray-500 mt-auto rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>