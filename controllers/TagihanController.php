<?php
class TagihanController {
    private $conn;
    private $table_name = "tagihan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data tagihan
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Membuat data tagihan baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (id_penggunaan, id_pelanggan, bulan, tahun, jumlah_meter, status, total_tagihan)
                  VALUES (:id_penggunaan, :id_pelanggan, :bulan, :tahun, :jumlah_meter, :status, :total_tagihan)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_penggunaan", $data->id_penggunaan);
        $stmt->bindParam(":id_pelanggan", $data->id_pelanggan);
        $stmt->bindParam(":bulan", $data->bulan);
        $stmt->bindParam(":tahun", $data->tahun);
        $stmt->bindParam(":jumlah_meter", $data->jumlah_meter);
        $stmt->bindParam(":status", $data->status);
        $stmt->bindParam(":total_tagihan", $data->total_tagihan);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tagihan berhasil ditambahkan"]);
        }
        return json_encode(["message" => "Gagal menambahkan tagihan"]);
    }

    // Memperbarui data tagihan
    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET jumlah_meter = :jumlah_meter, status = :status, total_tagihan = :total_tagihan
                  WHERE id_tagihan = :id_tagihan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tagihan", $data->id_tagihan);
        $stmt->bindParam(":jumlah_meter", $data->jumlah_meter);
        $stmt->bindParam(":status", $data->status);
        $stmt->bindParam(":total_tagihan", $data->total_tagihan);

        if ($stmt->execute()) {
            return json_encode(["message" => "Tagihan berhasil diperbarui"]);
        }
        return json_encode(["message" => "Gagal memperbarui tagihan"]);
    }

    // Menghapus data tagihan
    public function delete($id_tagihan) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_tagihan = :id_tagihan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tagihan", $id_tagihan);
        if ($stmt->execute()) {
            return json_encode(["message" => "Tagihan berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus tagihan"]);
    }
}
?>
