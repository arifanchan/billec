<!-- Mulai session -->
session_start();

<!-- Cek apakah token sudah diset atau belum -->
<?php
header("Location: public/index.php");
exit();

if (!isset($_SESSION['token'])) {
    header("Location: public/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=public/index.php">
    <script>
        window.location.href = "public/index.php";
    </script>
    <title>Redirecting...</title>
</head>
<body>
    <p>Jika Anda tidak dialihkan secara otomatis, klik <a href="public/index.php">di sini</a>.</p>
</body>
</html>