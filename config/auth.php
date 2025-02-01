<?php
/**
 * File konfigurasi autentikasi
 * 
 * File ini berisi konfigurasi autentikasi seperti nama session, lama token, dan algoritma hash.
 * 
 * @package Billec
 */
return [
    'session_name' => 'auth_session',
    'token_expiry' => 3600, // 1 jam
    'hash_algo' => PASSWORD_BCRYPT,
];
?>
