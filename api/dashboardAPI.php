<?php
/**
 * File ini berisi endpoint API untuk mendapatkan statistik dashboard
 */
include_once '../config/database.php'; // impor file database.php
include_once '../controllers/DashboardController.php'; // impor file DashboardController.php

header("Content-Type: application/json"); // set response berupa JSON
session_start();

$database = new Database(); // membuat objek dari class Database
$db = $database->getConnection(); // memanggil method getConnection
$dashboardController = new DashboardController($db); // membuat objek dari class DashboardController

// Cek apakah token sudah diset atau belum
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

// Cek role user
$role = $_SESSION['role'];
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null;

// Panggil method getDashboardStats
/**
 * Response body Admin:
 * - total_pelanggan: int
 * - total_tagihan: int
 * - tagihan_belum_bayar: int
 * - tagihan_lunas: int
 * - total_pembayaran: int
 * Response body Pelanggan:
 * - total_tagihan: int
 * - tagihan_belum_bayar: int
 * - tagihan_lunas: int
 * - total_pembayaran: int
 */
$data = $dashboardController->getDashboardStats($role, $id_pelanggan);
echo json_encode($data);
?>
