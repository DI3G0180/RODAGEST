<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('TRUNCATE TABLE users');
echo "Tabla users truncada exitosamente\n";
?>