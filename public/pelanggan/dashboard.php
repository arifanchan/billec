<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan</title>
</head>
<body>
    <h1>Selamat datang, <?= $_SESSION['user']['nama_pelanggan'] ?></h1>
    <nav>
        <a href="tagihan.php">Lihat Tagihan</a>
        <a href="../logout.php">Logout</a>
    </nav>
</body>
</html>
