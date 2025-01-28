<?php

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
    <title>Manajemen Tarif Listrik</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .form-container {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            width: 50%;
        }
        .form-container input {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 8px;
        }
        .form-container button {
            padding: 8px 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .error, .success {
            color: white;
            padding: 8px;
            margin-bottom: 10px;
        }
        .error {
            background-color: red;
        }
        .success {
            background-color: green;
        }
    </style>
</head>
<body>
    <h1>Manajemen Tarif Listrik</h1>
    <table>
        <thead>
            <tr>
                <th>ID Tarif</th>
                <th>Daya (VA)</th>
                <th>Tarif per kWh (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tarif-table-body">
            <!-- Data akan dimuat dengan JavaScript -->
        </tbody>
    </table>

    <div class="form-container">
        <h3>Tambah/Edit Tarif</h3>
        <form id="tarif-form">
            <input type="hidden" id="id_tarif" name="id_tarif">
            <label for="daya">Daya (VA):</label>
            <input type="number" id="daya" name="daya" required>
            <label for="tarifperkwh">Tarif per kWh (Rp):</label>
            <input type="number" id="tarifperkwh" name="tarifperkwh" step="0.01" required>
            <button type="submit">Simpan</button>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", async function () {
        const tableBody = document.getElementById("tarif-table-body");
        const form = document.getElementById("tarif-form");
        const idTarifInput = document.getElementById("id_tarif");
        const dayaInput = document.getElementById("daya");
        const tarifperkwhInput = document.getElementById("tarifperkwh");

        async function fetchTarif() {
            try {
                const response = await fetch("../../api/tarifAPI.php", {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                });

                if (!response.ok) throw new Error("Gagal memuat data tarif.");

                const data = await response.json();
                tableBody.innerHTML = data.map(t => `
                    <tr>
                        <td>${t.id_tarif}</td>
                        <td>${t.daya}</td>
                        <td>${t.tarifperkwh}</td>
                        <td>
                            <button onclick="editTarif(${t.id_tarif}, ${t.daya}, ${t.tarifperkwh})">Edit</button>
                            <button onclick="deleteTarif(${t.daya})">Hapus</button>
                        </td>
                    </tr>
                `).join("");
            } catch (error) {
                alert(error.message);
            }
        }

        async function saveTarif(e) {
            e.preventDefault();
            const idTarif = idTarifInput.value;
            const daya = parseInt(dayaInput.value);
            const tarifperkwh = parseFloat(tarifperkwhInput.value);

            try {
                const method = idTarif ? "PUT" : "POST";
                const response = await fetch("../../api/tarifAPI.php", {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id_tarif: idTarif, daya, tarifperkwh })
                });

                if (!response.ok) throw new Error("Gagal menyimpan data tarif.");

                const result = await response.json();
                alert(result.message);
                form.reset();
                fetchTarif();
            } catch (error) {
                alert(error.message);
            }
        }

        async function deleteTarif(daya) {
            if (!confirm("Apakah Anda yakin ingin menghapus tarif ini?")) return;

            try {
                const response = await fetch("../../api/tarifAPI.php", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ daya })
                });

                if (!response.ok) throw new Error("Gagal menghapus tarif.");

                const result = await response.json();
                alert(result.message);
                fetchTarif();
            } catch (error) {
                alert(error.message);
            }
        }

        window.editTarif = function (id_tarif, daya, tarifperkwh) {
            idTarifInput.value = id_tarif;
            dayaInput.value = daya;
            tarifperkwhInput.value = tarifperkwh;
        };

        window.deleteTarif = deleteTarif;

        form.addEventListener("submit", saveTarif);

        fetchTarif();
    });
    </script>
</body>
</html>
