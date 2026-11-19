<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$result = $pdo->query('DESCRIBE users');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . ($row['Null'] ?? 'NULL') . ' | ' . ($row['Key'] ?? 'KEY') . ' | ' . ($row['Default'] ?? 'N/A') . ' | ' . ($row['Extra'] ?? 'N/A') . "\n";
}
?>