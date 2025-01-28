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
    <title>Data Penggunaan Listrik</title>
    <script>
        async function fetchPenggunaan() {
            try {
                const response = await fetch('../../api/penggunaanAPI.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data penggunaan");
                }

                const penggunaan = await response.json();
                const tableBody = document.getElementById('penggunaan-table-body');
                tableBody.innerHTML = ""; // Kosongkan tabel sebelum menambahkan data

                penggunaan.forEach(p => {
                    const row = `
                        <tr>
                            <td>${p.id_pelanggan}</td>
                            <td>${p.nama_pelanggan}</td>
                            <td>${p.bulan}</td>
                            <td>${p.tahun}</td>
                            <td>${p.meter_awal}</td>
                            <td>${p.meter_akhir}</td>
                            <td>${p.total_penggunaan}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                alert(error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchPenggunaan);
    </script>
</head>
<body>
    <h2>Data Penggunaan Listrik</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Meter Awal</th>
                <th>Meter Akhir</th>
                <th>Total Pemakaian</th>
            </tr>
        </thead>
        <tbody id="penggunaan-table-body">
            <!-- Data penggunaan akan dimuat di sini melalui JavaScript -->
        </tbody>
    </table>
</body>
</html>
