<?php
include_once '../config/database.php';
include_once '../controllers/TagihanController.php';

header("Content-Type: application/json");
session_start();

$database = new Database();
$db = $database->getConnection();
$controller = new TagihanController($db);

$method = $_SERVER['REQUEST_METHOD'];

// Middleware: Periksa apakah pengguna sudah login
if (!isset($_SESSION['token'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized. Silakan login terlebih dahulu."]);
    exit;
}

// Middleware: Periksa role pengguna
$role = $_SESSION['role'];
$id_pelanggan = $_SESSION['user']['id_pelanggan'] ?? null;

switch ($method) {
    case 'GET':
        if ($role === 'pelanggan') {
            if ($id_pelanggan) {
                echo $controller->getByPelanggan($id_pelanggan);
            } else {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Tidak dapat mengakses data."]);
            }
        } elseif ($role === 'admin') {
            echo $controller->getAll();
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Role tidak valid."]);
        }
        break;

    case 'POST':
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->validatePayment($data);
        } elseif ($role === 'pelanggan' && isset($_FILES['bukti_pembayaran']) && isset($_POST['id_tagihan'])) {
            $id_tagihan = $_POST['id_tagihan'];
            $file = $_FILES['bukti_pembayaran'];

            // Cek apakah file diunggah dengan benar
            if ($file['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(["message" => "Gagal mengunggah file. Error: " . $file['error']]);
                exit;
            }

            // Pastikan file adalah gambar (opsional)
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowed_types)) {
                http_response_code(400);
                echo json_encode(["message" => "Format file tidak didukung. Hanya JPG dan PNG yang diperbolehkan."]);
                exit;
            }

            echo $controller->uploadBuktiPembayaran($id_tagihan, $file);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    case 'DELETE':
        if ($role === 'admin') {
            $data = json_decode(file_get_contents("php://input"));
            echo $controller->delete($data->id_tagihan);
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Forbidden. Anda tidak memiliki akses."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
