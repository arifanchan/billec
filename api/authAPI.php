<?php
include_once '../config/database.php';
include_once '../controllers/AuthController.php';

header("Content-Type: application/json");
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$db = $database->getConnection();
$controller = new AuthController($db);

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->action) && $data->action === "login") {
            echo $controller->login($data);
        } elseif (isset($data->action) && $data->action === "logout") {
            echo $controller->logout();
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
