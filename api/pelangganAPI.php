<?php
include_once '../config/database.php';
include_once '../controllers/PelangganController.php';

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];

$database = new Database();
$db = $database->getConnection();
$controller = new PelangganController($db);

switch ($method) {
    case 'GET':
        echo $controller->getAll();
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->create($data);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->update($data);
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->delete($data->id_pelanggan);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
