<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="../styles.css"> -->
    <title>Profil Admin</title>
    <script>
        async function fetchProfile() {
            const response = await fetch('../../api/userAPI.php');
            if (response.ok) {
                const admin = await response.json();
                document.getElementById('nama_admin').value = admin.nama_admin;
            } else {
                alert("Gagal memuat data profil");
            }
        }

        async function updateProfile(event) {
            event.preventDefault();
            const nama_admin = document.getElementById('nama_admin').value;
            const password = document.getElementById('password').value;

            const response = await fetch('../../api/userAPI.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nama_admin, password })
            });

            const result = await response.json();
            alert(result.message);

            if (response.ok) {
                location.reload(); // Reload halaman jika berhasil
            }
        }

        document.addEventListener('DOMContentLoaded', fetchProfile);
    </script>
</head>
<body>
<div class="form-container">
    <h1>Profil Admin</h1>
    <form id="profile-form" onsubmit="updateProfile(event)">
        <label for="nama_admin">Nama Lengkap:</label><br>
        <input type="text" id="nama_admin" name="nama_admin" required><br><br>

        <label for="password">Password Baru (opsional):</label><br>
        <input type="password" id="password" name="password"><br><br>

        <button type="submit">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>