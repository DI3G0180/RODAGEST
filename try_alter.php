<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec("ALTER TABLE users MODIFY role ENUM('admin', 'supervisor', 'inspector', 'intendente')");
    echo "Éxito!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>