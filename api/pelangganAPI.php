<?php
/**
 * File ini berisi endpoint API untuk entitas Pelanggan
 */
include_once '../config/database.php'; // impor file database.php
include_once '../controllers/PelangganController.php'; // impor file PelangganController.php

session_start();

header("Content-Type: application/json"); // set response berupa JSON
$method = $_SERVER['REQUEST_METHOD']; // ambil method request

// Middleware: Cek apakah pengguna sudah login
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

// Middleware: Batasi akses berdasarkan role
$role = $_SESSION['role']; // Role dari session
$userId = $_SESSION['user']['id_pelanggan'] ?? null; // ID pelanggan yang login

$database = new Database();
$db = $database->getConnection();
$controller = new PelangganController($db);

switch ($method) {
    case 'GET':
        // Hanya pelanggan yang bisa melihat data dirinya sendiri
        if ($role === 'pelanggan') {
            if (!$userId) {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
                exit;
            }

            // Ambil data pelanggan berdasarkan ID login
            echo $controller->getById($userId);
        } elseif ($role === 'admin') {
            // Admin bisa melihat semua data
            echo $controller->getAll();
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Role tidak valid."]);
        }
        break;

    case 'POST':
        // Hanya admin yang bisa menambahkan pelanggan
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->create($data);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $data->role = $role; // Tambahkan role ke data yang dikirim ke controller
    
        if ($role === 'pelanggan' && $data->id_pelanggan == $userId) {
            // Pelanggan hanya bisa mengubah data dirinya sendiri
            echo $controller->update($data);
        } elseif ($role === 'admin') {
            // Admin bisa mengubah data pelanggan manapun
            echo $controller->update($data);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'DELETE':
        // Hanya admin yang bisa menghapus pelanggan
        if ($role !== 'admin') {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->delete($data->id_pelanggan);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
