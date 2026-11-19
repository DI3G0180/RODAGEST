<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$result = $pdo->query("DESCRIBE users");
while ($row = $result->fetch()) {
    echo $row[0] . ' | ' . $row[1] . "\n";
}
$result2 = $pdo->query("SELECT id, role FROM users ORDER BY id");
while ($row = $result2->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Role: '" . $row['role'] . "'\n";
}
?>