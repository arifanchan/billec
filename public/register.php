<?php
session_start();

// Jika pengguna sudah login, tampilkan alert dan arahkan kembali ke index.php
if (isset($_SESSION['token'])) {
    echo "<script>
        alert('Anda harus logout terlebih dahulu sebelum melakukan pendaftaran.');
        window.location.href = 'index.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Billec</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
        }
        header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        header p {
            font-size: 1rem;
        }
        nav {
            background-color: #0056b3;
            color: white;
            display: flex;
            justify-content: center;
            padding: 0.5rem;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        main {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .register-container {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-container h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .register-container form {
            display: flex;
            flex-direction: column;
        }
        .register-container label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .register-container input,
        .register-container select {
            margin-bottom: 1rem;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .register-container button {
            background-color: #007BFF;
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
        }
        .register-container button:hover {
            background-color: #0056b3;
        }
        .login-link {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .login-link a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <h2>Billec - Daftar Akun</h2>
        <p>Silakan buat akun untuk mulai menggunakan layanan kami.</p>
    </header>
    <main>
        <div class="register-container">
            <form id="register-form">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="nomor_kwh">Nomor KWH</label>
                <input type="text" id="nomor_kwh" name="nomor_kwh" required>

                <label for="nama_pelanggan">Nama Lengkap</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>

                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" required>

                <label for="id_tarif">Daya Listrik</label>
                <select id="id_tarif" name="id_tarif" required>
                    <option value="">Pilih Daya</option>
                </select>

                <button type="submit">Daftar</button>
            </form>
            <p id="message" class="error"></p>
            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </main>
    <footer>
        <p>© 2025 <a href="https://github.com/arifanchan/billec" target="_blank">Billec</a> by 
        <a href="https://stackoverflow.com/users/19574157/arifa-chan" target="_blank">Arifa Nofriyaldi Chan</a>. 
        Semua Hak Dilindungi.</p>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", async function() {
        const tarifSelect = document.getElementById("id_tarif");

        try {
            const response = await fetch("../api/registerAPI.php?action=getTarif");
            if (!response.ok) throw new Error("Gagal memuat data tarif.");
            
            const data = await response.json();
            data.forEach(tarif => {
                tarifSelect.innerHTML += `<option value="${tarif.id_tarif}">${tarif.daya} VA</option>`;
            });
        } catch (error) {
            console.error("Error:", error);
        }
    });

    document.getElementById("register-form").addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const nomor_kwh = document.getElementById("nomor_kwh").value.trim();
        const nama_pelanggan = document.getElementById("nama_pelanggan").value.trim();
        const alamat = document.getElementById("alamat").value.trim();
        const id_tarif = document.getElementById("id_tarif").value;

        if (!username || !password || !nomor_kwh || !nama_pelanggan || !alamat || !id_tarif) {
            alert("Semua kolom harus diisi!");
            return;
        }

        const data = { username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif };

        try {
            const response = await fetch("../api/registerAPI.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            alert(result.message);

            if (response.ok) {
                window.location.href = "login.php";
            }
        } catch (error) {
            alert("Terjadi kesalahan, coba lagi nanti.");
        }
    });
    </script>
</body>
</html>
