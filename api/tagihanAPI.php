<?php
include_once '../config/database.php';
include_once '../controllers/TagihanController.php';

header("Content-Type: application/json");
$database = new Database();
$db = $database->getConnection();
$controller = new TagihanController($db);

$method = $_SERVER['REQUEST_METHOD'];
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
        echo $controller->delete($data->id_tagihan);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
