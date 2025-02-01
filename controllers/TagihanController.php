<?php

/**
 * Class TagihanController
 * Digunakan untuk mengatur data tagihan
 * @method getAll() : string
 * @method getByPelanggan($id_pelanggan) : string
 * @method validatePayment($data) : string
 * @method uploadBuktiPembayaran($id_tagihan, $file) : string
 * @method delete($id_tagihan) : string
 */
class TagihanController {
    private $conn;
    private $table_name = "view_tagihan_informatif"; // Menggunakan VIEW untuk informasi yang relevan

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data tagihan (untuk admin)
    /**
     * Function untuk mengambil semua data tagihan
     * Endpoint: /api/tagihanAPI.php
     * @return string
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Mendapatkan data tagihan berdasarkan id_pelanggan (untuk pelanggan)
    /**
     * Function untuk mengambil data tagihan berdasarkan ID pelanggan
     * Endpoint: /api/tagihanAPI.php?id_pelanggan=1
     * @param $id_pelanggan
     * @return string
     */
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Memperbarui status tagihan setelah validasi bukti pembayaran
    /**
     * Function untuk memvalidasi pembayaran
     * Endpoint: /api/tagihanAPI.php
     * @param $data
     * @return string
     */
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
    /**
     * Function untuk mengunggah bukti pembayaran
     * Endpoint: /api/tagihanAPI.php
     * @param $id_tagihan
     * @param $file
     * @return string
     */
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
    /**
     * Function untuk menghapus data tagihan
     * Endpoint: /api/tagihanAPI.php
     * @param $id_tagihan
     * @return string
     */
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
