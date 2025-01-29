<?php
session_start();

// Validasi token dan role pelanggan
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}

// Daftar halaman yang diizinkan
$allowed_pages = ['dashboard', 'penggunaan', 'tagihan', 'pembayaran', 'tarif', 'profil'];
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
</head>

<body>
    <header>
        <h1>Dashboard Pelanggan</h1>
        <p>Selamat datang, <strong><?= $username ?></strong>! Kelola tagihan listrik Anda dengan mudah.</p>
    </header>
    <nav>
        <a href="dashboard.php" class="<?= $page === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
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
        <!-- Statistik Dashboard -->
        <section class="dashboard-stats">
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
        <?php
        // Tampilkan halaman berdasarkan parameter
        switch ($page) {
            case 'dashboard':
                include_once 'dashboard.php';
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
                echo "<p>Silakan pilih menu di atas untuk mengakses fitur yang tersedia.</p>";
        }
        ?>
    </main>
    <footer>
        <p>© 2025 Billec. Semua Hak Dilindungi.</p>
    </footer>
</body>

</html>