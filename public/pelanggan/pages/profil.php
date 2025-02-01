<?php
/**
 * Halaman: Profil Pelanggan
 * File: profil.php
 * 
 * Tampilan profil pelanggan yang dapat diakses oleh pelanggan
 * 
 * @package Pelanggan
 */
// Validasi sesi pelanggan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}

// Ambil id pelanggan dari sesi untuk permintaan API
$id_pelanggan = $_SESSION['user']['id_pelanggan'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pelanggan</title>
    <link rel="stylesheet" href="../styles.css">
    <script>
        // Fungsi untuk mengambil data profil pelanggan dari API
        async function fetchProfile() {
            try {
                const response = await fetch(`../../api/pelangganAPI.php?id_pelanggan=<?= $id_pelanggan ?>`, {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) throw new Error("Gagal mengambil data profil.");

                const pelanggan = await response.json();
                document.getElementById('nama_pelanggan').value = pelanggan.nama_pelanggan;
                document.getElementById('alamat').value = pelanggan.alamat;
                document.getElementById('username').textContent = pelanggan.username;
            } catch (error) {
                alert(error.message);
            }
        }

        // Fungsi untuk memperbarui data profil pelanggan melalui API
        async function updateProfile(event) {
            event.preventDefault();

            const nama_pelanggan = document.getElementById("nama_pelanggan").value.trim();
            const alamat = document.getElementById("alamat").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!nama_pelanggan || !alamat) {
                alert("Nama dan alamat tidak boleh kosong!");
                return;
            }

            const data = { 
                id_pelanggan: <?= $id_pelanggan ?>, // Kirim ID pelanggan ke API
                nama_pelanggan, 
                alamat 
            };

            if (password) {
                data.password = password; // Hanya kirim password jika diisi
            }

            try {
                const response = await fetch('../../api/pelangganAPI.php', {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                alert(result.message);

                if (response.ok) {
                    fetchProfile(); // Ambil data terbaru setelah update
                }
            } catch (error) {
                alert("Terjadi kesalahan: " + error.message);
            }
        }

        document.addEventListener("DOMContentLoaded", fetchProfile);
    </script>
</head>
<body>
    <h1>Profil Pelanggan</h1>
    <div class="profile-box">
        <p><strong>Username:</strong> <span id="username"></span></p>
    </div><br>

    <h3>Ubah Profil</h3>
    <div class="form-container">
        <form id="profile-form" onsubmit="updateProfile(event)">
            <label for="nama_pelanggan">Nama Lengkap:</label>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" required>

            <label for="password">Password Baru (Opsional):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
