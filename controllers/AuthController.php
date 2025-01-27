<?php
class AuthController {
    private $conn;
    private $table_name = "user"; // Tabel untuk autentikasi

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi login
    public function login($data) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username AND password = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":password", $data->password);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Buat token (sederhana untuk contoh ini)
            $token = base64_encode(random_bytes(32));

            // Simpan token di sesi atau database (opsional)
            $_SESSION['token'] = $token;
            $_SESSION['user'] = $user;

            return json_encode([
                "message" => "Login berhasil",
                "token" => $token,
                "user" => [
                    "id_user" => $user['id_user'],
                    "username" => $user['username'],
                    "nama_admin" => $user['nama_admin']
                ]
            ]);
        }

        http_response_code(401);
        return json_encode(["message" => "Username atau password salah"]);
    }

    // Fungsi logout
    public function logout() {
        session_destroy();
        return json_encode(["message" => "Logout berhasil"]);
    }
}
?>
