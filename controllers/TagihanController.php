<?php
class TagihanController {
    private $conn;
    private $table_name = "view_tagihan_informatif";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data tagihan (untuk admin)
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Mendapatkan data tagihan berdasarkan id_pelanggan (untuk pelanggan)
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Memperbarui status tagihan setelah validasi bukti pembayaran
    public function validatePayment($data) {
        $query = "UPDATE tagihan 
                  SET status = 'lunas', bukti_pembayaran = :bukti_pembayaran 
                  WHERE id_tagihan = :id_tagihan AND status = 'belum bayar'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":bukti_pembayaran", $data->bukti_pembayaran);
        $stmt->bindParam(":id_tagihan", $data->id_tagihan);

        if ($stmt->execute()) {
            return json_encode(["message" => "Bukti pembayaran berhasil divalidasi. Status tagihan telah diperbarui."]);
        }
        return json_encode(["message" => "Gagal memvalidasi bukti pembayaran atau tagihan sudah lunas."]);
    }

    // Upload bukti pembayaran (khusus pelanggan)
    public function uploadBuktiPembayaran($id_tagihan, $file) {
        // Pastikan tagihan ada dan statusnya belum lunas
        $query = "SELECT * FROM tagihan WHERE id_tagihan = :id_tagihan AND status = 'belum bayar'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tagihan", $id_tagihan);
        $stmt->execute();
        $tagihan = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$tagihan) {
            http_response_code(404);
            return json_encode(["message" => "Tagihan tidak ditemukan atau sudah lunas."]);
        }
    
        // Direktori penyimpanan file
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        // Nama file unik
        $file_name = "bukti_" . $id_tagihan . "_" . time() . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . $file_name;
    
        // Pindahkan file ke folder uploads
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update database dengan nama file bukti pembayaran
            $query = "UPDATE tagihan SET bukti_pembayaran = :bukti_pembayaran WHERE id_tagihan = :id_tagihan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":bukti_pembayaran", $file_name);
            $stmt->bindParam(":id_tagihan", $id_tagihan);
    
            if ($stmt->execute()) {
                return json_encode(["message" => "Bukti pembayaran berhasil diunggah."]);
            } else {
                return json_encode(["message" => "Gagal menyimpan bukti pembayaran ke database."]);
            }
        } else {
            return json_encode(["message" => "Gagal mengunggah file."]);
        }
    }

    // Menghapus data tagihan (untuk admin)
    public function delete($id_tagihan) {
        $query = "DELETE FROM tagihan WHERE id_tagihan = :id_tagihan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tagihan", $id_tagihan);
        if ($stmt->execute()) {
            return json_encode(["message" => "Tagihan berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus tagihan"]);
    }
}
?>
