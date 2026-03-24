<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "Creating tables...\n";
    
    $sql = "
    CREATE TABLE IF NOT EXISTS mahasiswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nim VARCHAR(20) UNIQUE NOT NULL,
        nama VARCHAR(100) NOT NULL,
        no_telp VARCHAR(20),
        alamat TEXT
    );

    CREATE TABLE IF NOT EXISTS buku (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(200) NOT NULL,
        pengarang VARCHAR(100) NOT NULL,
        penerbit VARCHAR(100) NOT NULL,
        tahun_terbit INT,
        stok INT DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS peminjaman (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mahasiswa_id INT NOT NULL,
        buku_id INT NOT NULL,
        tanggal_pinjam DATETIME DEFAULT CURRENT_TIMESTAMP,
        tanggal_kembali DATETIME NULL,
        status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
        FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id),
        FOREIGN KEY (buku_id) REFERENCES buku(id)
    );
    ";
    
    try {
        $conn->exec($sql);
        echo "Tables created successfully\n";
    } catch (Exception $e) {
        echo "Error creating tables: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>