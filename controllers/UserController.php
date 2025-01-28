<?php
class UserController {
    private $conn;
    private $table_name = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan data admin berdasarkan ID
    public function getById($id_user) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_user = :id_user";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Memperbarui profil admin
    public function updateProfile($data) {
        $query = "UPDATE " . $this->table_name . " SET nama_admin = :nama_admin";
        if (!empty($data->password)) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id_user = :id_user";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama_admin", $data->nama_admin);
        if (!empty($data->password)) {
            $hashed_password = password_hash($data->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hashed_password);
        }
        $stmt->bindParam(":id_user", $data->id_user);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Profil berhasil diperbarui."];
        } else {
            return ["success" => false, "message" => "Terjadi kesalahan saat memperbarui profil."];
        }
    }
}
?>
