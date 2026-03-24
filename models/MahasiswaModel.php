<?php
class MahasiswaModel {
    private $conn;
    private $table = 'mahasiswa';
 
    public function __construct($db) {
        $this->conn = $db;
    }
 
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (nim, nama, no_telp, alamat) VALUES (:nim, :nama, :no_telp, :alamat)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':no_telp', $data['no_telp']);
        $stmt->bindParam(':alamat', $data['alamat']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
 
    // update dan delete serupa
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET nim = :nim, nama = :nama, no_telp = :no_telp, alamat = :alamat WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':no_telp', $data['no_telp']);
        $stmt->bindParam(':alamat', $data['alamat']);
        return $stmt->execute();
    }
 
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
 
    // Fungsi untuk mendapatkan jumlah peminjaman aktif mahasiswa
    public function getJumlahPeminjamanAktif($mahasiswa_id) {
        $query = "SELECT COUNT(*) as total FROM peminjaman WHERE mahasiswa_id = :mahasiswa_id AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
