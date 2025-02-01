<?php
/**
 * File ini berisi endpoint API untuk level
 */
include_once '../config/database.php'; // impor file database.php
include_once '../controllers/LevelController.php'; // impor file LevelController.php

header("Content-Type: application/json"); // set response berupa JSON
session_start();

$database = new Database(); // membuat objek dari class Database
$db = $database->getConnection(); // memanggil method getConnection
$controller = new LevelController($db); // membuat objek dari class LevelController

$method = $_SERVER['REQUEST_METHOD']; // ambil method request

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Forbidden. Hanya admin yang dapat mengakses."]);
    exit();
}

// Cek method request
switch ($method) {
    /**
     * Jika method yang digunakan adalah GET
     * Maka akan menampilkan semua data level
     * response body:
     * - id_level: int
     * - nama_level: string
     */
    case 'GET':
        echo $controller->getAll();
        break;
    /**
     * Jika method yang digunakan adalah POST
     * Maka akan menambahkan data level baru
     * request body:
     * - nama_level: string
     * response body:
     * - message: string
     */
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->create($data);
        break;
    /**
     * Jika method yang digunakan adalah DELETE
     * Maka akan menghapus data level berdasarkan id_level
     * request body:
     * - id_level: int
     * response body:
     * - message: string
     */
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->delete($data->id_level);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}
?>
