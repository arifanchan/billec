<?php
session_start();

// Validasi token dan role pelanggan
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}

// Daftar halaman yang diizinkan
$allowed_pages = ['profil', 'penggunaan', 'tagihan', 'tarif', 'pembayaran'];
$page = $_GET['page'] ?? 'home';

// Pastikan halaman valid
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Mendapatkan username pelanggan dari sesi
$username = htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Billec</title>
    <link rel="stylesheet" href="../styles.css">
    <script defer src="../scripts.js"></script>
    
</head>
<body>
    <header>
        <h1>Dashboard Pelanggan</h1>
        <p>Selamat datang, <strong><?= $username ?></strong>! Kelola tagihan listrik Anda dengan mudah.</p>
    </header>
    <nav>
        <a href="dashboard.php?page=profil" class="<?= $page === 'profil' ? 'active' : '' ?>">Profil</a>
        <a href="dashboard.php?page=penggunaan" class="<?= $page === 'penggunaan' ? 'active' : '' ?>">Data Penggunaan</a>
        <a href="dashboard.php?page=tagihan" class="<?= $page === 'tagihan' ? 'active' : '' ?>">Data Tagihan</a>
        <a href="dashboard.php?page=tarif" class="<?= $page === 'tarif' ? 'active' : '' ?>">Tarif Listrik</a>
        <a href="dashboard.php?page=pembayaran" class="<?= $page === 'pembayaran' ? 'active' : '' ?>">Data Pembayaran</a>
        <a href="../logout.php">Logout</a>
    </nav>
    <main>
        <?php
        // Tampilkan halaman berdasarkan parameter
        switch ($page) {
            case 'profil':
                include_once 'pages/profil.php';
                break;
            case 'penggunaan':
                include_once 'pages/penggunaan.php';
                break;
            case 'tagihan':
                include_once 'pages/tagihan.php';
                break;
            case 'tarif':
                include_once 'pages/tarif.php';
                break;
            case 'pembayaran':
                include_once 'pages/pembayaran.php';
                break;
            default:
                echo "<p>Silakan pilih menu di atas untuk mengakses fitur yang tersedia.</p>";
        }
        ?>
    </main>
    <footer>
        <p>© 2025 Billec. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>
