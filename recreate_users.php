<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Drop and recreate the users table
try {
    $pdo->exec("DROP TABLE users");
    echo "Tabla users eliminada\n";
} catch (PDOException $e) {
    echo "Error eliminando tabla: " . $e->getMessage() . "\n";
}

// Recreate with the correct enum including supervisor
try {
    $pdo->exec("
        CREATE TABLE users (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'supervisor', 'inspector', 'operador', 'intendente') NOT NULL DEFAULT 'operador',
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ");
    echo "Tabla users recreated exitosamente\n";
} catch (PDOException $e) {
    echo "Error recreando tabla: " . $e->getMessage() . "\n";
}
?>