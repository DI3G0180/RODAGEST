<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'inspector', 'operador', 'limpieza') NOT NULL DEFAULT 'operador'");
echo "Éxito: Enum actualizado a 'admin', 'inspector', 'operador', 'limpieza'\n";
?>