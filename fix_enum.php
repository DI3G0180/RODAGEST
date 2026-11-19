<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->exec('ALTER TABLE users MODIFY COLUMN role ENUM("admin", "supervisor", "inspector", "intendente")');
echo "OK: Enum actualizado\n";
?>