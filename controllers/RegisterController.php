<?php
class RegisterController {
    private $conn;
    private $table_name = "pelanggan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTarif() {
        $query = "SELECT id_tarif, daya FROM tarif";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function register($data) {
        $username = htmlspecialchars($data->username, ENT_QUOTES, 'UTF-8');
        $password = password_hash($data->password, PASSWORD_BCRYPT);
        $nomor_kwh = htmlspecialchars($data->nomor_kwh, ENT_QUOTES, 'UTF-8');
        $nama_pelanggan = htmlspecialchars($data->nama_pelanggan, ENT_QUOTES, 'UTF-8');
        $alamat = htmlspecialchars($data->alamat, ENT_QUOTES, 'UTF-8');
        $id_tarif = (int) $data->id_tarif;

        // Cek apakah username atau nomor_kwh sudah digunakan
        $check_query = "SELECT * FROM " . $this->table_name . " WHERE username = :username OR nomor_kwh = :nomor_kwh";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":username", $username);
        $check_stmt->bindParam(":nomor_kwh", $nomor_kwh);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            http_response_code(409);
            return json_encode(["message" => "Username atau nomor KWH sudah digunakan"]);
        }

        // Simpan data ke database
        $query = "INSERT INTO " . $this->table_name . " (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif)
                  VALUES (:username, :password, :nomor_kwh, :nama_pelanggan, :alamat, :id_tarif)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":nomor_kwh", $nomor_kwh);
        $stmt->bindParam(":nama_pelanggan", $nama_pelanggan);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":id_tarif", $id_tarif);

        if ($stmt->execute()) {
            http_response_code(201);
            return json_encode(["message" => "Pendaftaran berhasil, silakan login."]);
        }

        http_response_code(500);
        return json_encode(["message" => "Terjadi kesalahan, gagal mendaftar."]);
    }
}
?>
