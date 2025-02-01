<?php
/**
 * Halaman tagihan pelanggan
 * Pelanggan dapat melihat tagihan yang dimilikinya
 * Pelnaggan dapat mengunggah bukti pembayaran jika tagihan belum lunas
 * Pelnaggan dapat melihat bukti pembayaran yang sudah diunggah
 */

// Pastikan pengguna adalah pelanggan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Saya</title>
    <script>
        async function fetchTagihan() {
            try {
                const response = await fetch("../../api/tagihanAPI.php", {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data tagihan.");
                }

                const tagihan = await response.json();
                const tableBody = document.getElementById("tagihan-table-body");

                if (!tagihan || tagihan.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="9">Tidak ada data tagihan.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = tagihan.map(t => `
                    <tr>
                        <td>${t.bulan}</td>
                        <td>${t.tahun}</td>
                        <td>${t.nomor_kwh}</td>
                        <td>${t.daya_listrik} VA</td>
                        <td>${t.jumlah_meter}</td>
                        <td>${t.total_tagihan}</td>
                        <td>${t.status}</td>
                        <td>
                            ${t.bukti_pembayaran ? 
                                `<a href="../../uploads/${t.bukti_pembayaran}" target="_blank">Lihat</a>` : 
                                "Belum Upload"}
                        </td>
                        <td>
                            ${t.status === "belum bayar" ? 
                                `<form class="upload-form" onsubmit="uploadBukti(event, ${t.id_tagihan})" enctype="multipart/form-data">
                                    <input type="file" name="bukti_pembayaran" required>
                                    <button type="submit">Upload</button>
                                </form>` : 
                                ""}
                        </td>
                    </tr>
                `).join("");
            } catch (error) {
                alert(error.message);
                console.error("Error:", error);
            }
        }

        async function uploadBukti(event, idTagihan) {
            event.preventDefault();
            const formData = new FormData(event.target);

            try {
                formData.append("id_tagihan", idTagihan);
                const response = await fetch("../../api/tagihanAPI.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.message || "Gagal mengunggah bukti pembayaran.");
                }

                alert(result.message);
                fetchTagihan(); // Reload data setelah upload
            } catch (error) {
                alert(error.message);
                console.error("Error:", error);
            }
        }

        document.addEventListener("DOMContentLoaded", fetchTagihan);
    </script>
</head>
<body>
    <h2>Tagihan Saya</h2>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Nomor KWH</th>
                <th>Daya</th>
                <th>Jumlah Meter</th>
                <th>Total Tagihan</th>
                <th>Status</th>
                <th>Bukti Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tagihan-table-body">
            <!-- Data tagihan akan dimuat di sini -->
        </tbody>
    </table>
</body>
</html>
