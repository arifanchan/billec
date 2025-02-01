<?php
/**
 * File ini berisi endpoint API untuk pembayaran
 */
include_once '../config/database.php';
include_once '../controllers/PembayaranController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new PembayaranController($db);

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
            // Admin dapat melihat semua pembayaran
            echo $controller->getAll();
        } else {
        http_response_code(403);
        echo json_encode(["message" => "Forbidden. Role tidak valid."]);
        }
        break;

    case 'DELETE':
        // Hanya admin yang dapat menghapus pembayaran
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki izin untuk menghapus pembayaran."]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id_pembayaran)) {
            echo $controller->delete($data->id_pembayaran);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID pembayaran tidak ditemukan dalam permintaan."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
