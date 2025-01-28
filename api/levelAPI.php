<?php
include_once '../config/database.php';
include_once '../controllers/LevelController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new LevelController($db);

$method = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Forbidden. Hanya admin yang dapat mengakses."]);
    exit();
}

switch ($method) {
    case 'GET':
        echo $controller->getAll();
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->create($data);
        break;

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
