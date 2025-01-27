<?php
class PembayaranController {
    private $conn;
    private $table_name = "pembayaran";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mendapatkan semua data pembayaran
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    // Membuat data pembayaran baru
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (id_tagihan, id_pelanggan, tanggal_pembayaran, bulan_bayar, biaya_admin, total_bayar, id_user)
                  VALUES (:id_tagihan, :id_pelanggan, :tanggal_pembayaran, :bulan_bayar, :biaya_admin, :total_bayar, :id_user)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tagihan", $data->id_tagihan);
        $stmt->bindParam(":id_pelanggan", $data->id_pelanggan);
        $stmt->bindParam(":tanggal_pembayaran", $data->tanggal_pembayaran);
        $stmt->bindParam(":bulan_bayar", $data->bulan_bayar);
        $stmt->bindParam(":biaya_admin", $data->biaya_admin);
        $stmt->bindParam(":total_bayar", $data->total_bayar);
        $stmt->bindParam(":id_user", $data->id_user);

        if ($stmt->execute()) {
            return json_encode(["message" => "Pembayaran berhasil ditambahkan"]);
        }
        return json_encode(["message" => "Gagal menambahkan pembayaran"]);
    }

    // Menghapus data pembayaran
    public function delete($id_pembayaran) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pembayaran = :id_pembayaran";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pembayaran", $id_pembayaran);
        if ($stmt->execute()) {
            return json_encode(["message" => "Pembayaran berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus pembayaran"]);
    }
}
?>
