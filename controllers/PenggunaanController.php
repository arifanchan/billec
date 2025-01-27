<?php
class PenggunaanController {
    private $conn;
    private $table_name = "penggunaan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data (khusus admin)
    public function getAll() {
        $query = "SELECT * FROM view_penggunaan_listrik";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Mendapatkan data penggunaan berdasarkan id_pelanggan (khusus pelanggan)
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM view_penggunaan_listrik WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Membuat data baru (khusus admin)
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
