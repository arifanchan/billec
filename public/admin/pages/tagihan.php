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
    <title>Data Tagihan</title>
    <script>
        async function fetchTagihan() {
            try {
                const response = await fetch('../../api/tagihanAPI.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data tagihan");
                }

                const tagihan = await response.json();
                const tableBody = document.getElementById('tagihan-table-body');
                tableBody.innerHTML = ""; // Kosongkan tabel sebelum menambahkan data baru

                tagihan.forEach(t => {
                    const row = `
                        <tr>
                            <td>${t.id_tagihan}</td>
                            <td>${t.id_pelanggan}</td>
                            <td>${t.bulan}</td>
                            <td>${t.tahun}</td>
                            <td>${t.jumlah_meter}</td>
                            <td>${t.status}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                alert(error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchTagihan);
    </script>
</head>
<body>
    <h2>Data Tagihan</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID Tagihan</th>
                <th>ID Pelanggan</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Jumlah Meter</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="tagihan-table-body">
            <!-- Data tagihan akan dimuat di sini melalui JavaScript -->
        </tbody>
    </table>
</body>
</html>
