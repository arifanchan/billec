<?php
/**
 * Dashboard Admin
 * 
 * Halaman dashboard admin untuk mengelola data pelanggan, penggunaan, tagihan, pembayaran, tarif, dan profil.
 * 
 * @package Billec
 */
session_start();

// Validasi token dan role admin
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$allowed_pages = ['dashboard', 'pelanggan', 'penggunaan', 'tagihan', 'pembayaran', 'tarif', 'profil'];
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
    <link rel="stylesheet" href="../styles.css">
    <script defer src="../scripts.js"></script>
    <style>
        .dashboard-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
            margin: 20px 0;
        }

        .stat-box {
            flex: 1;
            min-width: 180px;
            padding: 20px;
            text-align: center;
            background-color: #f4f4f4;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-box h3 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }

        .stat-box p {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", async function () {
            async function fetchDashboardStats() {
                try {
                    const response = await fetch("../../api/dashboardAPI.php");
                    if (!response.ok) throw new Error("Gagal memuat data dashboard.");

                    const data = await response.json();
                    document.getElementById("total-pelanggan").textContent = data.total_pelanggan;
                    document.getElementById("total-tagihan").textContent = data.total_tagihan;
                    document.getElementById("tagihan-belum-bayar").textContent = data.tagihan_belum_bayar;
                    document.getElementById("tagihan-lunas").textContent = data.tagihan_lunas;
                    document.getElementById("total-pembayaran").textContent = `Rp ${data.total_pembayaran.toLocaleString()}`;
                } catch (error) {
                    console.error(error);
                    alert("Gagal memuat data dashboard.");
                }
            }

            fetchDashboardStats();
        });

    </script>
    <title>Dashboard Admin</title>
</head>

<body>
    <header>
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <?= $_SESSION['user']['nama_admin'] ?>! Anda masuk sebagai
            <strong><?= ucfirst($_SESSION['role']) ?></strong>.
        </p>
    </header>
    <nav>
        <a href="dashboard.php" class="<?= $page === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="dashboard.php?page=pelanggan" class="<?= $page === 'pelanggan' ? 'active' : '' ?>">Data Pelanggan</a>
        <a href="dashboard.php?page=penggunaan" class="<?= $page === 'penggunaan' ? 'active' : '' ?>">Data
            Penggunaan</a>
        <a href="dashboard.php?page=tagihan" class="<?= $page === 'tagihan' ? 'active' : '' ?>">Data Tagihan</a>
        <a href="dashboard.php?page=pembayaran" class="<?= $page === 'pembayaran' ? 'active' : '' ?>">Data
            Pembayaran</a>
        <a href="dashboard.php?page=tarif" class="<?= $page === 'tarif' ? 'active' : '' ?>">Tarif Listrik</a>
        <a href="dashboard.php?page=profil" class="<?= $page === 'profil' ? 'active' : '' ?>">Profil</a>
        <a href="../logout.php">Logout</a>
    </nav>
    <main>
    <p>Anda berada di: <strong><?= ucfirst($page) ?></strong></p>
    <!-- Statistik Dashboard -->
    <section class="dashboard-stats">
        <div class="stat-box">
            <h3>Total Pelanggan</h3>
            <p id="total-pelanggan">0</p>
        </div>
        <div class="stat-box">
            <h3>Total Tagihan</h3>
            <p id="total-tagihan">0</p>
        </div>
        <div class="stat-box">
            <h3>Tagihan Belum Bayar</h3>
            <p id="tagihan-belum-bayar">0</p>
        </div>
        <div class="stat-box">
            <h3>Tagihan Lunas</h3>
            <p id="tagihan-lunas">0</p>
        </div>
        <div class="stat-box">
            <h3>Total Pembayaran</h3>
            <p id="total-pembayaran">Rp 0</p>
        </div>
    </section>
    <div>
        <?php
        switch ($page) {
            case 'dashboard':
                include_once 'dashboard.php';
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
            case 'tarif':
                include_once 'pages/tarif.php';
                break;
            case 'profil':
                include_once 'pages/profil.php';
                break;
            default:
                echo "<p>Selamat datang di Dashboard Admin. Pilih menu di atas untuk mulai bekerja.</p>";
                break;
        }
        ?>
    </div>
    </main>
    <footer>
        <p>© 2025 Billec by Arifa Nofriyaldi Chan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>