<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($_SESSION['token'])) : ?>
        <p>Anda sudah login sebagai <?= $_SESSION['user']['username'] ?></p>
        <a href="logout.php">Logout</a>
    <?php else : ?>
        <form id="login-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <p id="message"></p>
    <?php endif; ?>

    <script>
        document.getElementById("login-form").addEventListener("submit", async function (e) {
            e.preventDefault();
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;

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
                document.getElementById("message").textContent = "Login berhasil!";
                window.location.reload(); // Reload halaman
            } else {
                document.getElementById("message").textContent = result.message;
            }
        });
    </script>
</body>
</html>
