<?php
session_start();

if (isset($_SESSION['token'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($_SESSION['role'] === 'pelanggan') {
        header("Location: pelanggan/dashboard.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
