<?php
/**
 * File konfigurasi autentikasi
 * 
 * File ini berisi konfigurasi autentikasi seperti nama session, lama token, dan algoritma hash.
 * 
 * @package Billec
 */
require_once __DIR__ . '/env.php'; // Load file env.php

loadEnv(__DIR__ . '/../.env');

return [
    'session_name' => 'auth_session',
    'token_expiry' => 3600, // 1 jam
    'hash_algo' => PASSWORD_BCRYPT,
    'secret_key' => getenv('SECRET_KEY')
];
?>
