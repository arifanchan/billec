<?php
include_once '../config/database.php';
include_once '../controllers/TagihanController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new TagihanController($db);

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
        // Pelanggan hanya bisa melihat tagihan miliknya sendiri
        if ($role === 'pelanggan') {
            if ($id_pelanggan) {
                echo $controller->getByPelanggan($id_pelanggan);
            } else {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Tidak dapat mengakses data."]);
            }
        } elseif ($role === 'admin') {
            // Admin dapat melihat semua tagihan
            echo $controller->getAll();
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Role tidak valid."]);
        }
        break;

    case 'POST':
        // Hanya admin yang boleh membuat data tagihan baru
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->create($data);
        
        // pelanggan dapat mengupload bukti pembayaran
        } elseif ($role === 'pelanggan') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->uploadBuktiBayar($id_tagihan, $data);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'PUT':
        // Hanya admin yang boleh memperbarui status pembayaran
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->update($data);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'DELETE':
        // Hanya admin yang boleh menghapus data tagihan
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->delete($data->id_tagihan);
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
