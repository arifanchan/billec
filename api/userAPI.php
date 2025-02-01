<?php
/**
 * File ini berisi endpoint API untuk mengelola data admin
 */
include_once '../config/database.php';
include_once '../controllers/UserController.php';

header("Content-Type: application/json");
session_start();

// Pastikan hanya admin yang dapat mengakses API ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Akses ditolak"]);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$userController = new UserController($db);

$method = $_SERVER['REQUEST_METHOD'];
$id_user = $_SESSION['user']['id_user'];

switch ($method) {
    case 'GET': // Ambil data admin
        $admin = $userController->getById($id_user);
        if ($admin) {
            echo json_encode($admin);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Data tidak ditemukan"]);
        }
        break;

    case 'PUT': // Perbarui data admin
        $data = json_decode(file_get_contents("php://input"));
        $data->id_user = $id_user;
        $result = $userController->updateProfile($data);
        if ($result['success']) {
            $_SESSION['user']['nama_admin'] = $data->nama_admin;
            echo json_encode(["message" => $result['message']]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => $result['message']]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
