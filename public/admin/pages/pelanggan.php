<?php

// Pastikan pengguna adalah admin
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
    <title>Data Pelanggan</title>
    <script>
        async function fetchPelanggan() {
            try {
                const response = await fetch('../../api/pelangganAPI.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data pelanggan");
                }

                const pelanggan = await response.json();
                const tableBody = document.getElementById('pelanggan-table-body');
                tableBody.innerHTML = ""; // Kosongkan tabel sebelum menambahkan data

                pelanggan.forEach(p => {
                    const row = `
                        <tr>
                            <td>${p.id_pelanggan}</td>
                            <td>${p.username}</td>
                            <td>${p.nama_pelanggan}</td>
                            <td>${p.alamat}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                alert(error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchPelanggan);
    </script>
</head>
<body>
    <h2>Data Pelanggan</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody id="pelanggan-table-body">
            <!-- Data pelanggan akan dimuat di sini melalui JavaScript -->
        </tbody>
    </table>
</body>
</html>
