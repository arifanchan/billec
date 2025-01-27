<?php
session_start();
if (!isset($_SESSION['token'])) {
    header("Location: ../index.php"); // Redirect ke login jika belum login
    exit();
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
    </style>
</head>
<body>
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?= $_SESSION['user']['nama_admin'] ?></p>
    
    <nav>
        <a href="dashboard.php?page=pelanggan">Data Pelanggan</a>
        <a href="dashboard.php?page=penggunaan">Data Penggunaan</a>
        <a href="dashboard.php?page=tagihan">Data Tagihan</a>
        <a href="dashboard.php?page=pembayaran">Data Pembayaran</a>
        <a href="../logout.php">Logout</a>
    </nav>

    <div>
        <?php
        $page = $_GET['page'] ?? 'home';

        switch ($page) {
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
                echo "<p>Silakan pilih menu di atas.</p>";
                break;
        }
        ?>
    </div>
</body>
</html>
