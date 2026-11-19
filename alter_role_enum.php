<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Drop and recreate the enum type
try {
    // MySQL doesn't allow altering enum directly, so we need to use ALTER TABLE MODIFY
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'inspector', 'operador', 'limpieza') NOT NULL DEFAULT 'operador'");
    echo "Éxito: Enum actualizado en la base de datos\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>