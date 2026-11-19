<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Drop the column and recreate it with the new enum
try {
    $pdo->exec("ALTER TABLE users DROP COLUMN role");
    // Re-add with new enum
    $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'supervisor', 'inspector', 'intendente') NOT NULL DEFAULT 'operador' AFTER password");
    echo "Éxito: Columna role recreada\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>