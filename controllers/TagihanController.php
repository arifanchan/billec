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

    // Mendapatkan data tagihan berdasarkan id_pelanggan (khusus pelanggan)
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
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

    public function uploadBuktiBayar($id_tagihan, $file) {
        // Periksa apakah tagihan ada
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_tagihan = :id_tagihan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_tagihan', $id_tagihan);
        $stmt->execute();
        $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$tagihan) {
            http_response_code(404);
            return json_encode(["message" => "Tagihan tidak ditemukan"]);
        }
    
        // Proses upload file
        $upload_dir = "../uploads/"; // Pastikan folder ini memiliki izin tulis
        $file_name = $id_tagihan . "_" . basename($file['name']);
        $target_file = $upload_dir . $file_name;
    
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update kolom bukti_bayar di database
            $query = "UPDATE " . $this->table_name . " SET bukti_bayar = :bukti_bayar WHERE id_tagihan = :id_tagihan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':bukti_bayar', $file_name);
            $stmt->bindParam(':id_tagihan', $id_tagihan);
    
            if ($stmt->execute()) {
                return json_encode(["message" => "Bukti pembayaran berhasil diunggah"]);
            } else {
                return json_encode(["message" => "Gagal menyimpan bukti pembayaran ke database"]);
            }
        } else {
            return json_encode(["message" => "Gagal mengunggah file bukti pembayaran"]);
        }
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
