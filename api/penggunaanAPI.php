<?php
include_once '../config/database.php';
include_once '../controllers/PenggunaanController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new PenggunaanController($db);

$method = $_SERVER['REQUEST_METHOD'];

// Middleware: Periksa apakah pengguna sudah login
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

// Middleware: Periksa role pengguna
$role = $_SESSION['role']; // Role disimpan di sesi (admin/pelanggan)
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null; // ID pelanggan hanya tersedia untuk pelanggan

switch ($method) {
    case 'GET':
        // Jika role adalah pelanggan, tampilkan hanya data miliknya sendiri
        if ($role === 'pelanggan') {
            if ($id_pelanggan) {
                echo $controller->getByPelanggan($id_pelanggan);
            } else {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Tidak dapat mengakses data."]);
            }
        } elseif ($role === 'admin') {
            // Admin dapat melihat semua data
            echo $controller->getAll();
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Role tidak valid."]);
        }
        break;

    case 'POST':
        // Hanya admin yang boleh menambahkan data penggunaan
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->create($data);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'PUT':
        // Hanya admin yang dapat memperbarui data
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->update($data);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'DELETE':
        // Hanya admin yang dapat menghapus data
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->delete($data->id_penggunaan);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
