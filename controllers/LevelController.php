<?php
class LevelController {
    private $conn;
    private $table_name = "level";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua level
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Menambahkan level baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nama_level)
                  VALUES (:nama_level)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama_level", $data->nama_level);

        if ($stmt->execute()) {
            return json_encode(["message" => "Level berhasil ditambahkan."]);
        }
        return json_encode(["message" => "Gagal menambahkan level."]);
    }

    // Menghapus level
    public function delete($id_level) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_level = :id_level";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_level", $id_level);

        if ($stmt->execute()) {
            return json_encode(["message" => "Level berhasil dihapus."]);
        }
        return json_encode(["message" => "Gagal menghapus level."]);
    }
}
?>
