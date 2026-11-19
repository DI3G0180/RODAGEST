<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'supervisor', 'inspector', 'operador', 'intendente') NOT NULL DEFAULT 'operador' AFTER password");
    echo "Columna role añadida exitosamente\n";
} catch (PDOException $e) {
    echo "Error añadiendo columna: " . $e->getMessage() . "\n";
}
?>