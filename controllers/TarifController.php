<?php
class TarifController {
    private $conn;
    private $table_name = "tarif";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data tarif
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Menambahkan tarif baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (daya, tarifperkwh)
                  VALUES (:daya, :tarifperkwh)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":daya", $data->daya);
        $stmt->bindParam(":tarifperkwh", $data->tarifperkwh);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil ditambahkan."]);
        }
        return json_encode(["message" => "Gagal menambahkan tarif."]);
    }

    // Memperbarui tarif
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET tarifperkwh = :tarifperkwh 
                  WHERE daya = :daya";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":daya", $data->daya);
        $stmt->bindParam(":tarifperkwh", $data->tarifperkwh);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil diperbarui."]);
        }
        return json_encode(["message" => "Gagal memperbarui tarif."]);
    }

    // Menghapus tarif
    public function delete($daya) {
        $query = "DELETE FROM " . $this->table_name . " WHERE daya = :daya";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":daya", $daya);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil dihapus."]);
        }
        return json_encode(["message" => "Gagal menghapus tarif."]);
    }
}
?>
