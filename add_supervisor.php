<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
try {
    $pdo->exec('ALTER TABLE users MODIFY COLUMN role ENUM("admin", "supervisor", "inspector", "intendente")');
    echo "Éxito: Enum actualizado\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>