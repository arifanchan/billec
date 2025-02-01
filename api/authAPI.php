<?php
/**
 * File ini berisi endpoint untuk login dan logout
 */

include_once '../config/database.php'; // impor file database.php
include_once '../controllers/AuthController.php'; // impor file AuthController.php

header("Content-Type: application/json"); // set response berupa JSON
session_start();

// Cek method request
$method = $_SERVER['REQUEST_METHOD'];

$database = new Database(); // membuat objek dari class Database
$db = $database->getConnection(); // memanggil method getConnection
$controller = new AuthController($db); // membuat objek dari class AuthController

switch ($method) {
    // Jika method yang digunakan adalah POST
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // Cek apakah data yang diterima adalah JSON
        $rawInput = file_get_contents("php://input");
        file_put_contents("debug.log", "Raw Input: " . $rawInput . PHP_EOL, FILE_APPEND);
        
        $data = json_decode($rawInput);
        file_put_contents("debug.log", "Decoded JSON: " . json_encode($data) . PHP_EOL, FILE_APPEND);
        
        // Jika data tidak valid
        if (!$data) {
            http_response_code(400);
            echo json_encode([
                "message" => "Invalid JSON or no data received",
                "rawInput" => $rawInput
            ]);
            exit;
        }

        // Cek aksi yang diminta
        if (isset($data->action) && $data->action === "login") {
            echo $controller->login($data); // panggil method login
        } elseif (isset($data->action) && $data->action === "logout") {
            echo $controller->logout(); // panggil method logout
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Aksi tidak valid"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
