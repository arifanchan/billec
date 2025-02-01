<?php
/**
 * Class DashboardController
 * 
 * @method getDashboardStats
 * @method getAdminStats
 * @method getPelangganStats
 * @method getCount
 * @method getSum
 * 
 * Class ini digunakan untuk mengatur data statistik dashboard
 */
class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Fungsi getDashboardStats
     * 
     * Fungsi ini digunakan untuk mendapatkan statistik dashboard
     * 
     * @param string $role
     * @param int $id_pelanggan
     * @return array
     * 
     * Endpoint: GET /api/dashboardAPI.php
     * 
     * Response body:
     * - total_pelanggan: int
     * - total_tagihan: int
     * - tagihan_belum_bayar: int
     * - tagihan_lunas: int
     * - total_pembayaran: int
     */
    public function getDashboardStats($role, $id_pelanggan = null) {
        if ($role === "admin") {
            return $this->getAdminStats();
        } elseif ($role === "pelanggan" && $id_pelanggan) {
            return $this->getPelangganStats($id_pelanggan);
        } else {
            return ["message" => "Role tidak valid"];
        }
    }

    /**
     * Fungsi getAdminStats
     * 
     * Fungsi ini digunakan untuk mendapatkan statistik dashboard untuk admin
     * 
     * @return array
     */
    private function getAdminStats() {
        return [
            "total_pelanggan" => $this->getCount("pelanggan"),
            "total_tagihan" => $this->getCount("tagihan"),
            "tagihan_belum_bayar" => $this->getCount("tagihan", "status = 'belum bayar'"),
            "tagihan_lunas" => $this->getCount("tagihan", "status = 'lunas'"),
            "total_pembayaran" => $this->getSum("pembayaran", "total_bayar")
        ];
    }

    /**
     * Fungsi getPelangganStats
     * 
     * Fungsi ini digunakan untuk mendapatkan statistik dashboard untuk pelanggan
     * 
     * @param int $id_pelanggan
     * @return array
     */
    private function getPelangganStats($id_pelanggan) {
        return [
            "total_tagihan" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan"),
            "tagihan_belum_bayar" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan AND status = 'belum bayar'"),
            "tagihan_lunas" => $this->getCount("tagihan", "id_pelanggan = $id_pelanggan AND status = 'lunas'"),
            "total_pembayaran" => $this->getSum("pembayaran", "total_bayar", "id_pelanggan = $id_pelanggan")
        ];
    }

    /**
     * Fungsi getCount
     * 
     * Fungsi ini digunakan untuk menghitung total baris di tabel
     * 
     * @param string $table
     * @param string $where
     * @return int
     */
    private function getCount($table, $where = "1") {
        $query = "SELECT COUNT(*) AS total FROM $table WHERE $where";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /* Fungsi getSum
     * 
     * Fungsi ini digunakan untuk menghitung total nilai kolom di tabel
     * 
     * @param string $table
     * @param string $column
     * @param string $where
     * @return int
     */
    private function getSum($table, $column, $where = "1") {
        $query = "SELECT SUM($column) AS total FROM $table WHERE $where";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
} 
?>

