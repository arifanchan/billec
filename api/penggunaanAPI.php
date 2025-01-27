<?php
include_once '../config/database.php';

header("Content-Type: application/json");
session_start();
$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Middleware: Periksa apakah pengguna sudah login
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

switch ($method) {
    case 'GET':
        $query = "SELECT * FROM view_penggunaan_listrik";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;
    case "POST":
        $query = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) VALUES (:id_pelanggan, :bulan, :tahun, :meter_awal, :meter_akhir)";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;
    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        $query = "UPDATE penggunaan SET meter_awal = :meter_awal, meter_akhir = :meter_akhir WHERE id_penggunaan = :id_penggunaan";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id_penggunaan", $data->id_penggunaan);
        $stmt->bindParam(":meter_awal", $data->meter_awal);
        $stmt->bindParam(":meter_akhir", $data->meter_akhir);
        $stmt->execute();
        echo json_encode(["message" => "Data penggunaan berhasil diperbarui"]);
        break;
    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        $query = "DELETE FROM penggunaan WHERE id_penggunaan = :id_penggunaan";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id_penggunaan", $data->id_penggunaan);
        $stmt->execute();
        echo json_encode(["message" => "Data penggunaan berhasil dihapus"]);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
