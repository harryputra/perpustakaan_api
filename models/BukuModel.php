<?php
class BukuModel {
    private $conn;
    private $table = 'buku';
 
    public function __construct($db) {
        $this->conn = $db;
    }
 
    // Ambil semua buku
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    // Ambil buku berdasarkan id
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    // Tambah buku
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (judul, pengarang, penerbit, tahun_terbit, stok) 
                  VALUES (:judul, :pengarang, :penerbit, :tahun_terbit, :stok)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':stok', $data['stok']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
 
    // Update buku
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET judul = :judul, pengarang = :pengarang, penerbit = :penerbit, 
                  tahun_terbit = :tahun_terbit, stok = :stok WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':pengarang', $data['pengarang']);
        $stmt->bindParam(':penerbit', $data['penerbit']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':stok', $data['stok']);
        return $stmt->execute();
    }
 
    // Hapus buku
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
 
    // Kurangi stok
    public function kurangiStok($id) {
        $query = "UPDATE " . $this->table . " SET stok = stok - 1 WHERE id = :id AND stok > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
 
    // Tambah stok (pengembalian)
    public function tambahStok($id) {
        $query = "UPDATE " . $this->table . " SET stok = stok + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
