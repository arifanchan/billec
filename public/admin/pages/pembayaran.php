<?php

/**
 * File laporan pembayaran
 * 
 * File ini digunakan untuk menampilkan laporan pembayaran yang telah dilakukan oleh pelanggan.
 * 
 * @package Billec
 */
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
    <title>Laporan Pembayaran</title>
    <!-- <link rel="stylesheet" href="../styles.css"> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
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

                if (pembayaran.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="10">Tidak ada data pembayaran.</td></tr>`;
                    return;
                }

                pembayaran.forEach(p => {
                    const row = `
                        <tr>
                            <td>${p.nama_pelanggan}</td>
                            <td>${p.nomor_kwh}</td>
                            <td>${p.daya_listrik} VA</td>
                            <td>${p.bulan}</td>
                            <td>${p.tahun}</td>
                            <td>${p.jumlah_meter} kWh</td>
                            <td>Rp ${p.biaya_admin.toLocaleString()}</td>
                            <td>${p.tanggal_pembayaran}</td>
                            <td>Rp ${p.total_bayar.toLocaleString()}</td>
                            <td>${p.nama_admin}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                alert(error.message);
                console.error("Error:", error);
            }
        }

        function cetakLaporan() {
            window.print();
        }

        function downloadPDF() {
            const element = document.getElementById('pembayaran-data');
            const options = {
                margin: 0.5,
                filename: 'laporan_pembayaran.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }

        document.addEventListener('DOMContentLoaded', fetchPembayaran);
    </script>
</head>
<body>
    <h2>Laporan Pembayaran</h2>
     <div class="action-buttons">
        <button onclick="cetakLaporan()">Cetak Laporan</button>
        <button onclick="downloadPDF()">Download PDF</button>
    </div>
    <div id="pembayaran-data">
        <table>
            <thead>
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>Nomor KWH</th>
                    <th>Daya</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Jumlah Meter</th>
                    <th>Biaya Admin</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Total Bayar</th>
                    <th>Admin Validasi</th>
                </tr>
            </thead>
            <tbody id="pembayaran-table-body">
                <!-- Data pembayaran akan dimuat di sini melalui JavaScript -->
            </tbody>
        </table>
    </div>
</body>
</html>
