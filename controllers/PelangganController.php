<?php

/**
 * Class PelangganController
 * Digunakan untuk mengatur data pelanggan
 * @method getAll() : string
 * @method getById($id_pelanggan) : string
 * @method create($data) : string
 * @method update($data) : string
 * @method delete($id_pelanggan) : string
 */
class PelangganController {
    private $conn;
    private $table_name = "view_pelanggan_relevan"; // Menggunakan VIEW untuk informasi yang relevan

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Function untuk mengambil semua data pelanggan
     * Endpoint: /api/pelangganAPI.php
     * @return string
     */
    // Mendapatkan semua data pelanggan (khusus admin)
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }
    
    /**
     * Function untuk mengambil data pelanggan berdasarkan ID pelanggan
     * Endpoint: /api/pelangganAPI.php?id_pelanggan=1
     * @param $id_pelanggan
     * @return string
     */
    public function getById($id_pelanggan) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($data) {
            return json_encode($data);
        }
    
        http_response_code(404);
        return json_encode(["message" => "Data pelanggan tidak ditemukan"]);
    }

    /**
     * Function untuk menambahkan data pelanggan
     * Endpoint: /api/pelangganAPI.php
     * @param $data
     * @return string
     */
    public function create($data) {
        // Menghindari SQL Injection
        $username = htmlspecialchars($data->username, ENT_QUOTES, 'UTF-8');
        $password = password_hash($data->password, PASSWORD_BCRYPT); // Hash password
        $nomor_kwh = htmlspecialchars($data->nomor_kwh, ENT_QUOTES, 'UTF-8');
        $nama_pelanggan = htmlspecialchars($data->nama_pelanggan, ENT_QUOTES, 'UTF-8');
        $alamat = htmlspecialchars($data->alamat, ENT_QUOTES, 'UTF-8');
        $id_tarif = (int) $data->id_tarif;

        $query = "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif)
                  VALUES (:username, :password, :nomor_kwh, :nama_pelanggan, :alamat, :id_tarif)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":nomor_kwh", $nomor_kwh);
        $stmt->bindParam(":nama_pelanggan", $nama_pelanggan);
        $stmt->bindParam(":alamat", $alamat);
        $stmt->bindParam(":id_tarif", $id_tarif);

        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil ditambahkan"]);
        }
        return json_encode(["message" => "Gagal menambahkan pelanggan"]);
    }

    /**
     * Function untuk mengupdate data pelanggan
     * Endpoint: /api/pelangganAPI.php
     * @param $data
     * @return string
     */
    public function update($data) {
        // Menghindari SQL Injection
        $nama_pelanggan = htmlspecialchars($data->nama_pelanggan, ENT_QUOTES, 'UTF-8');
        $alamat = htmlspecialchars($data->alamat, ENT_QUOTES, 'UTF-8');
        $id_pelanggan = (int) $data->id_pelanggan;
    
        // Jika pelanggan, hanya bisa mengubah beberapa field
        if (isset($data->role) && $data->role === 'pelanggan') {
            $query = "UPDATE pelanggan SET nama_pelanggan = :nama_pelanggan, alamat = :alamat";
            
            // Update password hanya jika tidak kosong
            if (!empty($data->password)) {
                $query .= ", password = :password";
            }
            
            $query .= " WHERE id_pelanggan = :id_pelanggan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nama_pelanggan", $nama_pelanggan);
            $stmt->bindParam(":alamat", $alamat);
            $stmt->bindParam(":id_pelanggan", $id_pelanggan);
    
            if (!empty($data->password)) {
                $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
                $stmt->bindParam(":password", $hashedPassword);
            }
        } else {
            // Admin bisa mengubah semua field termasuk username dan nomor_kwh
            $username = htmlspecialchars($data->username, ENT_QUOTES, 'UTF-8');
            $nomor_kwh = htmlspecialchars($data->nomor_kwh, ENT_QUOTES, 'UTF-8');
            $id_tarif = (int) $data->id_tarif;
    
            $query = "UPDATE pelanggan SET 
                      username = :username, nomor_kwh = :nomor_kwh, 
                      nama_pelanggan = :nama_pelanggan, alamat = :alamat, id_tarif = :id_tarif";
            
            // Update password hanya jika tidak kosong
            if (!empty($data->password)) {
                $query .= ", password = :password";
            }

            if (!empty($data->id_tarif)) {
                $query .= ", id_tarif = :id_tarif";
            }
    
            $query .= " WHERE id_pelanggan = :id_pelanggan";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":nomor_kwh", $nomor_kwh);
            $stmt->bindParam(":nama_pelanggan", $nama_pelanggan);
            $stmt->bindParam(":alamat", $alamat);
            $stmt->bindParam(":id_tarif", $id_tarif);
            $stmt->bindParam(":id_pelanggan", $id_pelanggan);
    
            if (!empty($data->password)) {
                $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
                $stmt->bindParam(":password", $hashedPassword);
            }
        }
    
        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil diperbarui"]);
        }
        return json_encode(["message" => "Gagal memperbarui pelanggan"]);
    }

    /**
     * Function untuk menghapus data pelanggan
     * Endpoint: /api/pelangganAPI.php
     * @param $id_pelanggan
     * @return string
     */
    public function delete($id_pelanggan) {
        $query = "DELETE FROM pelanggan WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);

        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus pelanggan"]);
    }
}
?>
