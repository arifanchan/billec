<?php

/**
 * Class PembayaranController
 * Digunakan untuk mengatur data pembayaran
 * @method getAll() : string
 * @method getByPelanggan($id_pelanggan) : string
 * @method delete($id_pembayaran) : string
 */
class PembayaranController {
    private $conn;
    private $table_name = "pembayaran";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Function untuk mengambil semua data pembayaran
     * Endpoint: /api/pembayaranAPI.php
     * @return string
     */
    public function getAll() {
        $query = "SELECT * FROM view_laporan_pembayaran"; // Menggunakan VIEW untuk informasi yang relevan
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    /**
     * Function untuk mengambil data pembayaran berdasarkan ID pelanggan
     * Endpoint: /api/pembayaranAPI.php?id_pelanggan=1
     * @param $id_pelanggan
     * @return string
     */
    public function getByPelanggan($id_pelanggan) {
        $query = "SELECT * FROM view_laporan_pembayaran WHERE id_pelanggan = :id_pelanggan"; // Menggunakan VIEW untuk informasi yang relevan
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_pelanggan', $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    /**
     * Function untuk menghapus data pembayaran
     * Endpoint: /api/pembayaranAPI.php
     * @param $id_pembayaran
     * @return string
     */
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
