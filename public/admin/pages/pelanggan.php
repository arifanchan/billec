<?php
/**
 * Halaman manajemen pelanggan
 * 
 * File ini berisi halaman manajemen pelanggan yang hanya dapat diakses oleh admin.
 * 
 * @package Billec
 */
// Pastikan pengguna adalah admin

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan</title>
    <!-- <link rel="stylesheet" href="../styles.css"> -->
</head>
<body>
    <h1>Manajemen Pelanggan</h1>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Nomor KWH</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Daya Listrik</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="pelanggan-table-body">
            <!-- Data pelanggan akan dimuat dengan JavaScript -->
        </tbody>
    </table>

    <div class="form-container">
        <h3>Tambah/Edit Pelanggan</h3>
        <form id="pelanggan-form">
            <input type="hidden" id="id_pelanggan" name="id_pelanggan">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password Baru (Optional):</label>
            <input type="password" id="password" name="password">
            <label for="nomor_kwh">Nomor KWH:</label>
            <input type="text" id="nomor_kwh" name="nomor_kwh" required>
            <label for="nama_pelanggan">Nama Pelanggan:</label>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>
            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" required>
            <label for="id_tarif">Daya Listrik:</label>
            <select id="id_tarif" name="id_tarif" required>
                <option value="" select="selected">Pilih Daya</option>
                <option value="1">450 VA</option>
                <option value="2">900 VA</option>
                <option value="3">1300 VA</option>
                <option value="4">2300 VA</option>
                <option value="5">3500 VA</option>
                <option value="6">5500 VA</option>
            </select>
            <button type="submit">Simpan</button>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", async function () {
        const tableBody = document.getElementById("pelanggan-table-body");
        const form = document.getElementById("pelanggan-form");
        const idPelangganInput = document.getElementById("id_pelanggan");
        const usernameInput = document.getElementById("username");
        const passwordInput = document.getElementById("password");
        const nomorKwhInput = document.getElementById("nomor_kwh");
        const namaPelangganInput = document.getElementById("nama_pelanggan");
        const alamatInput = document.getElementById("alamat");
        const idTarifInput = document.getElementById("id_tarif");

        async function fetchPelanggan() {
            try {
                const response = await fetch("../../api/pelangganAPI.php", {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) throw new Error("Gagal memuat data pelanggan.");

                const data = await response.json();
                tableBody.innerHTML = data.map(p => `
                    <tr>
                        <td>${p.username}</td>
                        <td>${p.nomor_kwh}</td>
                        <td>${p.nama_pelanggan}</td>
                        <td>${p.alamat}</td>
                        <td>${p.daya_listrik} VA</td>
                        <td>
                            <button onclick="editPelanggan(${p.id_pelanggan}, '${p.username}', '${p.nomor_kwh}', '${p.nama_pelanggan}', '${p.alamat}', ${p.id_tarif})">Edit</button>
                            <button onclick="deletePelanggan(${p.id_pelanggan})">Hapus</button>
                        </td>
                    </tr>
                `).join("");
            } catch (error) {
                alert(error.message);
            }
        }

        async function savePelanggan(e) {
            e.preventDefault();
            const idPelanggan = idPelangganInput.value;
            const password = passwordInput.value ? passwordInput.value : null; // Jika password kosong, maka null
            const data = {
                id_pelanggan: idPelanggan,
                username: usernameInput.value,
                password: passwordInput.value || null,
                nomor_kwh: nomorKwhInput.value,
                nama_pelanggan: namaPelangganInput.value,
                alamat: alamatInput.value,
                id_tarif: idTarifInput.value
            };

            try {
                const method = idPelanggan ? "PUT" : "POST";
                const response = await fetch("../../api/pelangganAPI.php", {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                });

                if (!response.ok) throw new Error("Gagal menyimpan data pelanggan.");

                const result = await response.json();
                alert(result.message);
                form.reset();
                fetchPelanggan();
            } catch (error) {
                alert(error.message);
            }
        }

        async function deletePelanggan(id) {
            if (!confirm("Apakah Anda yakin ingin menghapus pelanggan ini?")) return;

            try {
                const response = await fetch("../../api/pelangganAPI.php", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_pelanggan: id })
                });

                if (!response.ok) throw new Error("Gagal menghapus pelanggan.");

                const result = await response.json();
                alert(result.message);
                fetchPelanggan();
            } catch (error) {
                alert(error.message);
            }
        }

        window.editPelanggan = function (id, username, nomorKwh, namaPelanggan, alamat, idTarif) {
            idPelangganInput.value = id;
            usernameInput.value = username;
            nomorKwhInput.value = nomorKwh;
            namaPelangganInput.value = namaPelanggan;
            alamatInput.value = alamat;
            idTarifInput.value = idTarif;
        };

        window.deletePelanggan = deletePelanggan;

        form.addEventListener("submit", savePelanggan);

        fetchPelanggan();
    });
    </script>
</body>
</html>
