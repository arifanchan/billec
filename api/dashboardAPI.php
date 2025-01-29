<?php
include_once '../config/database.php';
include_once '../controllers/DashboardController.php';

header("Content-Type: application/json");
session_start();

if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$dashboardController = new DashboardController($db);

$role = $_SESSION['role'];
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null;

$data = $dashboardController->getDashboardStats($role, $id_pelanggan);
echo json_encode($data);
?>
