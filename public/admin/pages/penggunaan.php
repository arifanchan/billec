<?php

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php"); // Redirect jika bukan admin atau belum login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Penggunaan Listrik</title>
    <!-- <link rel="stylesheet" href="../styles.css"> -->
</head>
<body>
    <h1>Manajemen Penggunaan Listrik</h1>

    <!-- Tabel Data Penggunaan -->
    <table>
        <thead>
            <tr>
                <th>Nama Pelanggan</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Meter Awal</th>
                <th>Meter Akhir</th>
                <th>Total Pemakaian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="penggunaan-table-body">
            <!-- Data penggunaan akan dimuat di sini melalui JavaScript -->
        </tbody>
    </table>

    <!-- Form Tambah/Edit Penggunaan -->
    <div class="form-container">
        <h3>Tambah/Edit Penggunaan</h3>
        <form id="penggunaan-form">
            <input type="hidden" id="id_penggunaan" name="id_penggunaan">
            <label for="id_pelanggan">Pilih Pelanggan:</label>
            <select id="id_pelanggan" name="id_pelanggan" required>
                <!-- Opsi pelanggan akan dimuat di sini melalui JavaScript -->
            </select>
            <label for="bulan">Bulan:</label>
            <input type="number" id="bulan" name="bulan" min="1" max="12" required>
            <label for="tahun">Tahun:</label>
            <input type="number" id="tahun" name="tahun" required>
            <label for="meter_awal">Meter Awal:</label>
            <input type="number" id="meter_awal" name="meter_awal" required>
            <label for="meter_akhir">Meter Akhir:</label>
            <input type="number" id="meter_akhir" name="meter_akhir" required>
            <button type="submit">Simpan</button>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.getElementById("penggunaan-table-body");
        const form = document.getElementById("penggunaan-form");
        const pelangganSelect = document.getElementById("id_pelanggan");

        // Memuat data pelanggan untuk pilihan dropdown
        async function fetchPelanggan() {
            try {
                const response = await fetch("../../api/pelangganAPI.php", {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) throw new Error("Gagal memuat data pelanggan.");

                const pelanggan = await response.json();
                pelangganSelect.innerHTML = pelanggan.map(p => `
                    <option value="${p.id_pelanggan}">${p.nama_pelanggan} - ${p.nomor_kwh} (${p.daya_listrik} VA)</option>
                `).join("");
            } catch (error) {
                alert(error.message);
            }
        }

        // Memuat data penggunaan
        async function fetchPenggunaan() {
            try {
                const response = await fetch("../../api/penggunaanAPI.php", {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) throw new Error("Gagal memuat data penggunaan.");

                const data = await response.json();
                tableBody.innerHTML = data.map(p => `
                    <tr>
                        <td>${p.nama_pelanggan}</td>
                        <td>${p.bulan}</td>
                        <td>${p.tahun}</td>
                        <td>${p.meter_awal}</td>
                        <td>${p.meter_akhir}</td>
                        <td>${p.total_penggunaan}</td>
                        <td>
                            <button onclick="editPenggunaan(${p.id_penggunaan}, ${p.id_pelanggan}, '${p.bulan}', '${p.tahun}', ${p.meter_awal}, ${p.meter_akhir})">Edit</button>
                            <button onclick="deletePenggunaan(${p.id_penggunaan})">Hapus</button>
                        </td>
                    </tr>
                `).join("");
            } catch (error) {
                alert(error.message);
            }
        }

        async function savePenggunaan(event) {
            event.preventDefault();
            const idPenggunaan = document.getElementById("id_penggunaan").value;
            const idPelanggan = document.getElementById("id_pelanggan").value;
            const bulan = document.getElementById("bulan").value;
            const tahun = document.getElementById("tahun").value;
            const meterAwal = document.getElementById("meter_awal").value;
            const meterAkhir = document.getElementById("meter_akhir").value;

            const payload = { id_pelanggan: idPelanggan, bulan, tahun, meter_awal: meterAwal, meter_akhir: meterAkhir };
            if (idPenggunaan) payload.id_penggunaan = idPenggunaan;

            try {
                const method = idPenggunaan ? "PUT" : "POST";
                const response = await fetch("../../api/penggunaanAPI.php", {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) throw new Error("Gagal menyimpan data penggunaan.");

                const result = await response.json();
                alert(result.message);
                form.reset();
                fetchPenggunaan();
            } catch (error) {
                alert(error.message);
            }
        }

        async function deletePenggunaan(idPenggunaan) {
            if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;

            try {
                const response = await fetch("../../api/penggunaanAPI.php", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_penggunaan: idPenggunaan })
                });

                if (!response.ok) throw new Error("Gagal menghapus data penggunaan.");

                const result = await response.json();
                alert(result.message);
                fetchPenggunaan();
            } catch (error) {
                alert(error.message);
            }
        }

        window.editPenggunaan = function (id_penggunaan, id_pelanggan, bulan, tahun, meter_awal, meter_akhir) {
            document.getElementById("id_penggunaan").value = id_penggunaan;
            document.getElementById("id_pelanggan").value = id_pelanggan; // ID pelanggan tetap dimasukkan secara tersembunyi
            document.getElementById("bulan").value = bulan;
            document.getElementById("tahun").value = tahun;
            document.getElementById("meter_awal").value = meter_awal;
            document.getElementById("meter_akhir").value = meter_akhir;
        };

        form.addEventListener("submit", savePenggunaan);
        fetchPelanggan(); // Memuat opsi pelanggan
        fetchPenggunaan(); // Memuat data penggunaan
    });
    </script>
</body>
</html>
