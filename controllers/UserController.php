<?php

/**
 * Class UserController
 * 
 * Class ini digunakan untuk mengatur data admin
 * @method getById($id_user) : array
 * @method updateProfile($data) : array
 */
class UserController {
    private $conn;
    private $table_name = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan data admin berdasarkan ID
    /**
     * Function untuk mengambil data admin berdasarkan ID
     * Endpoint: /api/userAPI.php?id_user=1
     * @param $id_user
     * @return array
     */
    public function getById($id_user) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_user = :id_user";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_user", $id_user);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Memperbarui profil admin
    /**
     * Function untuk memperbarui profil admin
     * Endpoint: /api/userAPI.php
     * @param $data
     * @return array
     */
    public function updateProfile($data) {
        $query = "UPDATE " . $this->table_name . " SET nama_admin = :nama_admin";
        // Jika password kosong, maka password tidak diupdate
        if (!empty($data->password)) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id_user = :id_user";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama_admin", $data->nama_admin);
        // Jika password tidak kosong, maka password akan diupdate
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
