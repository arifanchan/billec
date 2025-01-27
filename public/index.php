<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            max-width: 300px;
        }
        input {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.5rem;
        }
        button {
            padding: 0.5rem 1rem;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($_SESSION['token'])) : ?>
        <p>Anda sudah login sebagai <strong><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <a href="logout.php">Logout</a>
    <?php else : ?>
        <form id="login-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p id="message" class="error"></p>
    <?php endif; ?>

    <script>
    document.getElementById("login-form").addEventListener("submit", async function (e) {
        e.preventDefault();

        // Ambil nilai input
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        // Validasi input di sisi klien
        if (username === "" || password === "") {
            document.getElementById("message").textContent = "Username dan password tidak boleh kosong.";
            return;
        }

        try {
            // Kirim permintaan login ke API
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
                // Redirect berdasarkan peran
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
