<?php
include_once '../config/database.php';
include_once '../controllers/RegisterController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new RegisterController($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTarif') {
    echo $controller->getTarif();
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    echo $controller->register($data);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
