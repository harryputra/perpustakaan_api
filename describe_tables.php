<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "Database connection successful\n";
    
    $tables = ['buku', 'mahasiswa', 'peminjaman'];
    foreach ($tables as $table) {
        echo "Table: $table\n";
        try {
            $stmt = $conn->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col) {
                echo "  " . $col['Field'] . " " . $col['Type'] . "\n";
            }
        } catch (Exception $e) {
            echo "Error describing $table: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>