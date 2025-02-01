<?php
/**
 * File migrasi
 * 
 * File ini berisi script untuk melakukan migrasi password pelanggan ke hash baru
 * 
 * @package Billec
 */
include_once '../config/database.php';
session_start();
$database = new Database();
$db = $database->getConnection();
// Ambil semua pelanggan dengan password lama
$query = "SELECT id_pelanggan, password FROM pelanggan";
$stmt = $db->query($query);
$pelanggans = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pelanggans as $pelanggan) {
    // Hash ulang password
    $hashed_password = password_hash($pelanggan['password'], PASSWORD_DEFAULT);

    // Update password di database
    $update_query = "UPDATE pelanggan SET password = :password WHERE id_pelanggan = :id_pelanggan";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':password', $hashed_password);
    $update_stmt->bindParam(':id_pelanggan', $pelanggan['id_pelanggan']);
    $update_stmt->execute();

    echo "Password untuk pelanggan ID {$pelanggan['id_pelanggan']} berhasil dihash.\n";
}
?>
