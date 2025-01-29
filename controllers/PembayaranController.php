<?php
class PembayaranController {
    private $conn;
    private $table_name = "pembayaran";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data pembayaran (untuk admin)
    public function getAll() {
        $query = "SELECT * FROM view_laporan_pembayaran";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Mendapatkan data pembayaran berdasarkan pelanggan (untuk pelanggan)
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM view_laporan_pembayaran WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_pelanggan', $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($data) {
            return json_encode($data);
        } else {
            http_response_code(404);
            return json_encode(["message" => "Tidak ada pembayaran untuk pelanggan ini."]);
        }
    }

    // Menghapus data pembayaran (hanya admin)
    public function delete($id_pembayaran) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pembayaran = :id_pembayaran";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_pembayaran', $id_pembayaran);

        if ($stmt->execute()) {
            return json_encode(["message" => "Pembayaran berhasil dihapus"]);
        } else {
            return json_encode(["message" => "Gagal menghapus pembayaran"]);
        }
    }
}
?>
