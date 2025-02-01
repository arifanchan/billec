<?php

/**
 * Class TarifController
 * Digunakan untuk mengatur data tarif
 * @method getAll() : string
 * @method create($data) : string
 * @method update($data) : string
 * @method delete($id_tarif) : string
 */
class TarifController {
    private $conn;
    private $table_name = "tarif";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data tarif
    /**
     * Function untuk mengambil semua data tarif
     * Endpoint: /api/tarifAPI.php
     * @return string
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Menambahkan tarif baru
    /**
     * Function untuk menambahkan data tarif
     * Endpoint: /api/tarifAPI.php
     * @param $data
     * @return string
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (daya, tarifperkwh) VALUES (:daya, :tarifperkwh)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":daya", $data->daya);
        $stmt->bindParam(":tarifperkwh", $data->tarifperkwh);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil ditambahkan."]);
        }
        return json_encode(["message" => "Gagal menambahkan tarif."]);
    }

    // Memperbarui tarif berdasarkan id_tarif
    /**
     * Function untuk memperbarui data tarif
     * Endpoint: /api/tarifAPI.php
     * @param $data
     * @return string
     */
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET tarifperkwh = :tarifperkwh 
                  WHERE id_tarif = :id_tarif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tarif", $data->id_tarif);
        $stmt->bindParam(":tarifperkwh", $data->tarifperkwh);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil diperbarui."]);
        }
        return json_encode(["message" => "Gagal memperbarui tarif."]);
    }

    // Menghapus tarif berdasarkan id_tarif
    /**
     * Function untuk menghapus data tarif
     * Endpoint: /api/tarifAPI.php
     * @param $id_tarif
     * @return string
     */
    public function delete($id_tarif) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_tarif = :id_tarif";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tarif", $id_tarif);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tarif berhasil dihapus."]);
        }
        return json_encode(["message" => "Gagal menghapus tarif."]);
    }
}
