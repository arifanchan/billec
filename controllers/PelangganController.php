<?php
class PelangganController {
    private $conn;
    private $table_name = "pelanggan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($data);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif)
                  VALUES (:username, :password, :nomor_kwh, :nama_pelanggan, :alamat, :id_tarif)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":password", $data->password);
        $stmt->bindParam(":nomor_kwh", $data->nomor_kwh);
        $stmt->bindParam(":nama_pelanggan", $data->nama_pelanggan);
        $stmt->bindParam(":alamat", $data->alamat);
        $stmt->bindParam(":id_tarif", $data->id_tarif);
        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil ditambahkan"]);
        }
        return json_encode(["message" => "Gagal menambahkan pelanggan"]);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, password = :password, nomor_kwh = :nomor_kwh, 
                      nama_pelanggan = :nama_pelanggan, alamat = :alamat, id_tarif = :id_tarif
                  WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $data->id_pelanggan);
        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":password", $data->password);
        $stmt->bindParam(":nomor_kwh", $data->nomor_kwh);
        $stmt->bindParam(":nama_pelanggan", $data->nama_pelanggan);
        $stmt->bindParam(":alamat", $data->alamat);
        $stmt->bindParam(":id_tarif", $data->id_tarif);
        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil diperbarui"]);
        }
        return json_encode(["message" => "Gagal memperbarui pelanggan"]);
    }

    public function delete($id_pelanggan) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_pelanggan = :id_pelanggan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pelanggan", $id_pelanggan);
        if ($stmt->execute()) {
            return json_encode(["message" => "Pelanggan berhasil dihapus"]);
        }
        return json_encode(["message" => "Gagal menghapus pelanggan"]);
    }
}
?>
