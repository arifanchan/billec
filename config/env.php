<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception(".env file not found at $filePath");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Abaikan komentar
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Pecah key=value
        list($key, $value) = explode('=', $line, 2);

        // Bersihkan spasi atau tanda kutip di sekitar value
        $key = trim($key);
        $value = trim($value);

        // Set variabel environment
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
?>
