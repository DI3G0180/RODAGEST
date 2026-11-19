<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$result = $pdo->query("SELECT id, role FROM users ORDER BY id");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Role: '" . $row['role'] . "'\n";
}
?>