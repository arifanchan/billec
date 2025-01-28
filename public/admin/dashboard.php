<?php
session_start();

// Validasi token dan role admin
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$allowed_pages = ['profil', 'pelanggan', 'tarif', 'penggunaan', 'tagihan', 'pembayaran'];
$page = $_GET['page'] ?? 'home';

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        nav {
            margin-bottom: 20px;
        }
        nav a {
            margin-right: 10px;
            text-decoration: none;
            color: blue;
        }
        .active {
            font-weight: bold;
            color: darkblue;
        }
    </style>
</head>
<body>
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?= $_SESSION['user']['nama_admin'] ?>! Anda masuk sebagai <strong><?= ucfirst($_SESSION['role']) ?></strong>.</p>
    <nav>
        <a href="dashboard.php?page=profil" class="<?= $page === 'profil' ? 'active' : '' ?>">Profil</a>
        <a href="dashboard.php?page=pelanggan" class="<?= $page === 'pelanggan' ? 'active' : '' ?>">Data Pelanggan</a>
        <a href="dashboard.php?page=tarif" class="<?= $page === 'tarif' ? 'active' : '' ?>">Tarif Listrik</a>
        <a href="dashboard.php?page=penggunaan" class="<?= $page === 'penggunaan' ? 'active' : '' ?>">Data Penggunaan</a>
        <a href="dashboard.php?page=tagihan" class="<?= $page === 'tagihan' ? 'active' : '' ?>">Data Tagihan</a>
        <a href="dashboard.php?page=pembayaran" class="<?= $page === 'pembayaran' ? 'active' : '' ?>">Data Pembayaran</a>
        <a href="../logout.php">Logout</a>
    </nav>
    <p>Anda berada di: <strong><?= ucfirst($page) ?></strong></p>
    <div>
        <?php
        switch ($page) {
            case 'profil':
                include_once 'pages/profil.php';
                break;
            case 'tarif':
                include_once 'pages/tarif.php';
                break;
            case 'pelanggan':
                include_once 'pages/pelanggan.php';
                break;
            case 'penggunaan':
                include_once 'pages/penggunaan.php';
                break;
            case 'tagihan':
                include_once 'pages/tagihan.php';
                break;
            case 'pembayaran':
                include_once 'pages/pembayaran.php';
                break;
            default:
                echo "<p>Selamat datang di Dashboard Admin. Pilih menu di atas untuk mulai bekerja.</p>";
                break;
        }
        ?>
    </div>
</body>
</html>
