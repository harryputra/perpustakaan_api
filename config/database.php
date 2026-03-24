<?php
/**
 * Konfigurasi koneksi database menggunakan PDO
 * 
 * @return PDO Objek koneksi database
 */
class Database {
    private $host = 'localhost';
    private $dbname = 'perpustakaandb';
    private $username = 'root';
    private $password = ''; // kosong untuk XAMPP default
    private $conn;
 
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
