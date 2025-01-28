<?php
include_once '../config/database.php';
include_once '../controllers/TarifController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new TarifController($db);

$method = $_SERVER['REQUEST_METHOD'];

// Middleware: Periksa apakah pengguna sudah login
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit();
}

// Dapatkan peran pengguna (admin/pelanggan)
$role = $_SESSION['role']; // Role disimpan di sesi (admin/pelanggan)
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null; // ID pelanggan hanya tersedia untuk pelanggan

switch ($method) {
    case 'GET':
        // Semua pengguna dapat melihat data tarif
        echo $controller->getAll();
        break;

    case 'POST':
        // Hanya admin yang dapat melakukan operasi POST
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));

            if (isset($data->daya, $data->tarifperkwh)) {
                echo $controller->create($data);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap. Pastikan daya dan tarifperkwh terisi."]);
            }
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        } 
    case 'PUT':
        // Hanya admin yang dapat melakukan operasi PUT
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));

            if (isset($data->daya, $data->tarifperkwh)) {
                echo $controller->update($data);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap. Pastikan daya dan tarifperkwh terisi."]);
            }
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
    case 'DELETE':
        // Hanya admin yang dapat melakukan operasi POST, PUT, DELETE
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));

            if (isset($data->daya)) {
                echo $controller->delete($data->daya);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap. Pastikan daya terisi."]);
            }
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}
?>
