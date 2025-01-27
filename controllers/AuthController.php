<?php

class AuthController {
    private $conn;
    private $table_user = "user";       // Tabel admin
    private $table_pelanggan = "pelanggan"; // Tabel pelanggan

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi login
    public function login($data) {
        // Validasi input (pastikan username dan password tidak kosong)
        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);
            return json_encode(["message" => "Username dan password harus diisi"]);
        }

        // Sanitasi input untuk mencegah serangan XSS
        $username = htmlspecialchars($data->username, ENT_QUOTES, 'UTF-8');
        $password = $data->password;
        
        // Coba login sebagai admin (tabel `user`)
        $query_admin = "SELECT * FROM " . $this->table_user . " WHERE username = :username";
        $stmt_admin = $this->conn->prepare($query_admin);
        $stmt_admin->bindParam(":username", $username);
        $stmt_admin->execute();

        if ($stmt_admin->rowCount() > 0) {
            $user = $stmt_admin->fetch(PDO::FETCH_ASSOC);

            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $user['password'])) {
                // Buat token untuk admin
                $token = base64_encode(random_bytes(32));
                $_SESSION['token'] = $token;
                $_SESSION['user'] = $user;
                $_SESSION['role'] = 'admin';
                session_regenerate_id(true);
                return json_encode([
                    "message" => "Login berhasil sebagai admin",
                    "token" => $token,
                    "role" => "admin",
                    "user" => [
                        "id_user" => $user['id_user'],
                        "username" => $user['username'],
                        "nama_admin" => $user['nama_admin']
                    ]
                ]);
            }
        }

        // Jika bukan admin, coba login sebagai pelanggan (tabel `pelanggan`)
        $query_pelanggan = "SELECT * FROM " . $this->table_pelanggan . " WHERE username = :username";
        $stmt_pelanggan = $this->conn->prepare($query_pelanggan);
        $stmt_pelanggan->bindParam(":username", $username);
        $stmt_pelanggan->execute();

        if ($stmt_pelanggan->rowCount() > 0) {
            $pelanggan = $stmt_pelanggan->fetch(PDO::FETCH_ASSOC);

            // Verifikasi password menggunakan password_verify
            if (password_verify($password, $pelanggan['password'])) {
                // Buat token untuk pelanggan
                $token = base64_encode(random_bytes(32));
                $_SESSION['token'] = $token;
                $_SESSION['user'] = $pelanggan;
                $_SESSION['role'] = 'pelanggan';
                session_regenerate_id(true);
                return json_encode([
                    "message" => "Login berhasil sebagai pelanggan",
                    "token" => $token,
                    "role" => "pelanggan",
                    "user" => [
                        "id_pelanggan" => $pelanggan['id_pelanggan'],
                        "username" => $pelanggan['username'],
                        "nama_pelanggan" => $pelanggan['nama_pelanggan']
                    ]
                ]);
            }
        }

        // Jika tidak ditemukan
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
