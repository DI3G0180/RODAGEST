<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Truncate users table
try {
    $pdo->exec("TRUNCATE TABLE users");
    echo "Tabla users truncada\n";
} catch (PDOException $e) {
    echo "Error truncando: " . $e->getMessage() . "\n";
}

// Now alter the enum
try {
    $pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'inspector', 'intendente') NOT NULL DEFAULT 'operador'");
    echo "Enum actualizado después de truncate\n";
} catch (PDOException $e) {
    echo "Error alterando enum: " . $e->getMessage() . "\n";
}
?>