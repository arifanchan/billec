<?php
/**
 * File ini berisi endpoint API untuk tarif
 */

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
$role = $_SESSION['role'];
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null;

switch ($method) {
    case 'GET':
        echo $controller->getAll();
        break;

    case 'POST':
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
        break;

    case 'PUT':
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            if (isset($data->id_tarif, $data->tarifperkwh)) {
                echo $controller->update($data);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap. Pastikan id_tarif dan tarifperkwh terisi."]);
            }
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'DELETE':
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            if (isset($data->id_tarif)) {
                echo $controller->delete($data->id_tarif);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Data tidak lengkap. Pastikan id_tarif terisi."]);
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
