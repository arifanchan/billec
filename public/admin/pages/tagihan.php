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
    <title>Manajemen Tagihan</title>
    <!-- <link rel="stylesheet" href="../styles.css"> -->
</head>
<body>
    <h2>Manajemen Tagihan</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Pelanggan</th>
                <th>Nomor KWH</th>
                <th>Daya (VA)</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Jumlah Meter</th>
                <th>Total Tagihan (Rp)</th>
                <th>Bukti Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="tagihan-table-body">
            <!-- Data tagihan akan dimuat di sini -->
        </tbody>
    </table>

    <div id="message" class="success" style="display: none;"></div>

    <script>
        document.addEventListener("DOMContentLoaded", async function () {
    const tableBody = document.getElementById("tagihan-table-body");
    const messageBox = document.getElementById("message");

    // Fungsi untuk memuat data tagihan
    async function fetchTagihan() {
        try {
            const response = await fetch("../../api/tagihanAPI.php", {
                method: "GET",
                headers: { "Content-Type": "application/json" }
            });

            if (!response.ok) {
                throw new Error("Gagal memuat data tagihan.");
            }

            const data = await response.json();
            tableBody.innerHTML = data.map(t => {
                let statusElement = "";

                if (t.status === 'belum bayar') {
                    if (t.bukti_pembayaran) {
                        // Jika ada bukti pembayaran, tampilkan tombol validasi
                        statusElement = `<button onclick="validatePayment(${t.id_tagihan}, '${t.bukti_pembayaran}')">Validasi</button>`;
                    } else {
                        // Jika belum ada bukti pembayaran, tampilkan "Belum Bayar"
                        statusElement = `<span class="text-danger">Belum Bayar</span>`;
                    }
                } else {
                    // Jika status lunas, tampilkan statusnya saja
                    statusElement = `<span class="text-success">${t.status}</span>`;
                }

                return `
                    <tr>
                        <td>${t.nama_pelanggan}</td>
                        <td>${t.nomor_kwh}</td>
                        <td>${t.daya_listrik}</td>
                        <td>${t.bulan}</td>
                        <td>${t.tahun}</td>
                        <td>${t.jumlah_meter}</td>
                        <td>${t.total_tagihan}</td>
                        <td>
                            ${t.bukti_pembayaran 
                                ? `<a href="../../uploads/${t.bukti_pembayaran}" target="_blank">Lihat</a>` 
                                : `<span class="text-warning">Belum Upload</span>`}
                        </td>
                        <td>${statusElement}</td>
                    </tr>
                `;
            }).join("");
        } catch (error) {
            alert(error.message);
        }
    }

    // Fungsi untuk validasi pembayaran
    async function validatePayment(idTagihan, buktiPembayaran) {
        if (!confirm("Apakah Anda yakin ingin memvalidasi bukti pembayaran ini?")) return;

        try {
            const response = await fetch("../../api/tagihanAPI.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_tagihan: idTagihan,
                    bukti_pembayaran: buktiPembayaran
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || "Gagal memvalidasi pembayaran.");
            }

            messageBox.style.display = "block";
            messageBox.textContent = result.message;
            messageBox.className = "success";
            fetchTagihan(); // Reload data setelah validasi
        } catch (error) {
            messageBox.style.display = "block";
            messageBox.textContent = error.message;
            messageBox.className = "error";
        }
    }

    window.validatePayment = validatePayment;
    fetchTagihan(); // Load data tagihan saat halaman dimuat
});    </script>
</body>
</html>
