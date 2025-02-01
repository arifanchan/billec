<?php
/**
 * Halaman: penggunaan.php
 * Tujuan: Menampilkan data penggunaan listrik pelanggan
 * Fitur: Pelanggan dapat melihat data penggunaan listrik mereka
 */
// Pastikan pengguna adalah pelanggan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penggunaan Listrik | Billec</title>
</head>
<body>

        <h2>Penggunaan Listrik Saya</h2>
        <p>Lihat dan pantau penggunaan listrik Anda dengan mudah di bawah ini:</p>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Meter Awal</th>
                    <th>Meter Akhir</th>
                    <th>Total Penggunaan (kWh)</th>
                </tr>
            </thead>
            <tbody id="penggunaan-table-body">
                <!-- Data akan dimuat dengan JavaScript -->
            </tbody>
        </table>
    <script>
    document.addEventListener("DOMContentLoaded", async function () {
        try {
            const response = await fetch("../../api/penggunaanAPI.php", {
                method: "GET",
                headers: { "Content-Type": "application/json" }
            });
            if (!response.ok) {
                throw new Error("Gagal memuat data penggunaan");
            }
            const penggunaan = await response.json();
            const tableBody = document.getElementById("penggunaan-table-body");

            if (!penggunaan || penggunaan.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="9">Tidak ada data penggunaan.</td></tr>`;
                    return;
                }
            penggunaan.forEach(p => {
                const row = `
                    <tr>
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
    });
    </script>
</body>
</html>
