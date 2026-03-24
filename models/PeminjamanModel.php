<?php
class PeminjamanModel {
    private $conn;
    private $table = 'peminjaman';
 
    public function __construct($db) {
        $this->conn = $db;
    }
 
    public function getAll() {
        $query = "SELECT p.*, m.nama as nama_mahasiswa, b.judul as judul_buku 
                  FROM " . $this->table . " p
                  JOIN mahasiswa m ON p.mahasiswa_id = m.id
                  JOIN buku b ON p.buku_id = b.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public function getById($id) {
        $query = "SELECT p.*, m.nama as nama_mahasiswa, b.judul as judul_buku 
                  FROM " . $this->table . " p
                  JOIN mahasiswa m ON p.mahasiswa_id = m.id
                  JOIN buku b ON p.buku_id = b.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (mahasiswa_id, buku_id, tanggal_pinjam, status) 
                  VALUES (:mahasiswa_id, :buku_id, NOW(), 'dipinjam')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $data['mahasiswa_id']);
        $stmt->bindParam(':buku_id', $data['buku_id']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
 
    // Pengembalian: update tanggal_kembali dan status
    public function returnBook($id) {
        $query = "UPDATE " . $this->table . " SET tanggal_kembali = NOW(), status = 'dikembalikan' WHERE id = :id AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
 
    // Cek apakah mahasiswa sudah meminjam buku tertentu (belum dikembalikan)
    public function isBookBorrowedByMahasiswa($mahasiswa_id, $buku_id) {
        $query = "SELECT id FROM " . $this->table . " WHERE mahasiswa_id = :mahasiswa_id AND buku_id = :buku_id AND status = 'dipinjam'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
        $stmt->bindParam(':buku_id', $buku_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
 
    // Fungsi untuk mendapatkan riwayat peminjaman mahasiswa
    public function getRiwayatByMahasiswa($mahasiswa_id) {
        $query = "SELECT p.*, b.judul as judul_buku 
                  FROM " . $this->table . " p
                  JOIN buku b ON p.buku_id = b.id
                  WHERE p.mahasiswa_id = :mahasiswa_id
                  ORDER BY p.tanggal_pinjam DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
