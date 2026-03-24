<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "Database connection successful\n";
    
    // Test query
    try {
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables: " . implode(", ", $tables) . "\n";
    } catch (Exception $e) {
        echo "Error querying tables: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>