<?php
require_once 'config/database.php';
require_once 'controllers/BukuController.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $controller = new BukuController($db);
    
    echo "Testing getAll...\n";
    $controller->getAll();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>