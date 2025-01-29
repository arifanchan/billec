<?php
class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDashboardStats($role, $id_pelanggan = null) {
        if ($role === "admin") {
            return $this->getAdminStats();
        } elseif ($role === "pelanggan" && $id_pelanggan) {
            return $this->getPelangganStats($id_pelanggan);
        } else {
            return ["message" => "Role tidak valid"];
        }
    }

    // Statistik untuk Admin
    private function getAdminStats() {
        return [
            "total_pelanggan" => $this->getCount("pelanggan"),
            "total_tagihan" => $this->getCount("tagihan"),
            "tagihan_belum_bayar" => $this->getCount("tagihan", "status = 'belum bayar'"),
            "tagihan_lunas" => $this->getCount("tagihan", "status = 'lunas'"),
            "total_pembayaran" => $this->getSum("pembayaran", "total_bayar")
        ];
    }

    // Statistik untuk Pelanggan
    private function getPelangganStats($id_pelanggan) {
        return [
            "total_tagihan" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan"),
            "tagihan_belum_bayar" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan AND status = 'belum bayar'"),
            "tagihan_lunas" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan AND status = 'lunas'"),
            "total_pembayaran" => $this->getSum("pembayaran", "total_bayar", "id_pelanggan = $id_pelanggan")
        ];
    }

    // Helper: Menghitung jumlah data di tabel
    private function getCount($table, $where = "1") {
        $query = "SELECT COUNT(*) AS total FROM $table WHERE $where";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Helper: Menghitung total sum dari suatu kolom di tabel
    private function getSum($table, $column, $where = "1") {
        $query = "SELECT SUM($column) AS total FROM $table WHERE $where";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}
?>
