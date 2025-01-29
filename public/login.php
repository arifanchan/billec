<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billec - Tagihan Listrik</title>
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
        .hero {
            text-align: center;
            margin-bottom: 2rem;
        }
        .hero h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1rem;
            color: #555;
        }
        .login-container {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .login-container input {
            margin-bottom: 1rem;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .login-container button {
            background-color: #007BFF;
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
        footer p {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Billec - Tagihan Listrik</h1>
        <p>Solusi Mudah untuk Pembayaran Tagihan Listrik Pascabayar Anda</p>
    </header>
    <nav>
        <a href="#">Home</a>
        <a href="#">Tentang</a>
        <a href="#">Hubungi Kami</a>
    </nav>
    <main>
        <div class="hero">
            <h2>Bayar Tagihan Listrik dengan Cepat, Aman, dan Nyaman</h2>
            <p>Selalu terkoneksi dan bayar tagihan Anda kapan saja, di mana saja.</p>
        </div>
        <div class="login-container">
            <h3>Login ke Akun Anda</h3>
            <form id="login-form">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Login</button>
            </form>
            <p id="message" class="error"></p>
        </div>
    </main>
    <footer>
        <p>© 2025 Billec. Semua Hak Dilindungi.</p>
    </footer>

    <script>
    document.getElementById("login-form").addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        if (username === "" || password === "") {
            document.getElementById("message").textContent = "Username dan password tidak boleh kosong.";
            return;
        }

        try {
            const response = await fetch("../api/authAPI.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    action: "login",
                    username: username,
                    password: password
                })
            });

            const result = await response.json();

            if (response.ok) {
                if (result.role === "admin") {
                    window.location.href = "admin/dashboard.php";
                } else if (result.role === "pelanggan") {
                    window.location.href = "pelanggan/dashboard.php";
                }
            } else {
                document.getElementById("message").textContent = result.message;
            }
        } catch (error) {
            document.getElementById("message").textContent = "Terjadi kesalahan saat menghubungi server. Silakan coba lagi.";
        }
    });
    </script>
</body>
</html>
