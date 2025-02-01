<?php

/**
 * Class PenggunaanController
 * Digunakan untuk mengatur data penggunaan
 * @method getAll() : string
 * @method getByPelanggan($id_pelanggan) : string
 * @method create($data) : string
 * @method update($data) : string
 * @method delete($id_penggunaan) : string
 */
class PenggunaanController {
    private $conn;
    private $table_name = "penggunaan";

    public function __construct($db) {
        $this->conn = $db;
    }


    /**
     * Function untuk mengambil semua data penggunaan
     * Endpoint: /api/penggunaanAPI.php
     * @return string
     */
    public function getAll() {
        $query = "SELECT * FROM view_penggunaan_listrik"; // Menggunakan VIEW untuk informasi yang relevan
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Mendapatkan data penggunaan berdasarkan id_pelanggan (khusus pelanggan)
    /**
     * Function untuk mengambil data penggunaan berdasarkan ID pelanggan
     * Endpoint: /api/penggunaanAPI.php?id_pelanggan=1
     * @param $id_pelanggan
     * @return string
     */
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM view_penggunaan_listrik WHERE id_pelanggan = :id_pelanggan"; // Menggunakan VIEW untuk informasi yang relevan
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Membuat data baru (khusus admin)
    /**
     * Function untuk menambahkan data penggunaan
     * Endpoint: /api/penggunaanAPI.php
     * @param $data
     * @return string
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (id_pelanggan, bulan, tahun, meter_awal, meter_akhir)
                  VALUES (:id_pelanggan, :bulan, :tahun, :meter_awal, :meter_akhir)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $data->id_pelanggan);
        $stmt->bindParam(":bulan", $data->bulan);
        $stmt->bindParam(":tahun", $data->tahun);
        $stmt->bindParam(":meter_awal", $data->meter_awal);
        $stmt->bindParam(":meter_akhir", $data->meter_akhir);
        if ($stmt->execute()) {
            return json_encode(["message" => "Data penggunaan berhasil ditambahkan"]);
        }
        return json_encode(["message" => "Gagal menambahkan data penggunaan"]);
    }

    // Memperbarui data (khusus admin)
    /**
     * Function untuk memperbarui data penggunaan
     * Endpoint: /api/penggunaanAPI.php
     * @param $data
     * @return string
     */
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET meter_awal = :meter_awal, meter_akhir = :meter_akhir 
                  WHERE id_penggunaan = :id_penggunaan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_penggunaan", $data->id_penggunaan);
        $stmt->bindParam(":meter_awal", $data->meter_awal);
        $stmt->bindParam(":meter_akhir", $data->meter_akhir);
        if ($stmt->execute()) {
            return json_encode(["message" => "Data penggunaan berhasil diperbarui"]);
        }
        return json_encode(["message" => "Gagal memperbarui data penggunaan"]);
    }

    // Menghapus data (khusus admin)
    /**
     * Function untuk menghapus data penggunaan
     * Endpoint: /api/penggunaanAPI.php
     * @param $id_penggunaan
     * @return string
     */
    public function delete($id_penggunaan) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_penggunaan = :id_penggunaan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_penggunaan", $id_penggunaan);
        if ($stmt->execute()) {
            return json_encode(["message" => "Data penggunaan berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus data penggunaan"]);
    }
}
?>
