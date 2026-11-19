<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$result = $pdo->query('SELECT email, role FROM users ORDER BY email');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['email'] . ' -> ' . $row['role'] . "\n";
}
?>