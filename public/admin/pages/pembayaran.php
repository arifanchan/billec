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
    <title>Data Pembayaran</title>
    <script>
        async function fetchPembayaran() {
            try {
                const response = await fetch('../../api/pembayaranAPI.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data pembayaran");
                }

                const pembayaran = await response.json();
                const tableBody = document.getElementById('pembayaran-table-body');
                tableBody.innerHTML = ""; // Kosongkan tabel sebelum menambahkan data baru

                pembayaran.forEach(p => {
                    const row = `
                        <tr>
                            <td>${p.id_pembayaran}</td>
                            <td>${p.id_tagihan}</td>
                            <td>${p.id_pelanggan}</td>
                            <td>${p.tanggal_pembayaran}</td>
                            <td>${p.total_bayar}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                alert(error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchPembayaran);
    </script>
</head>
<body>
    <h2>Data Pembayaran</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID Pembayaran</th>
                <th>ID Tagihan</th>
                <th>ID Pelanggan</th>
                <th>Tanggal Pembayaran</th>
                <th>Total Bayar</th>
            </tr>
        </thead>
        <tbody id="pembayaran-table-body">
            <!-- Data pembayaran akan dimuat di sini melalui JavaScript -->
        </tbody>
    </table>
</body>
</html>
