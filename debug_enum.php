<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$result = $pdo->query("DESCRIBE users");
while ($row = $result->fetch()) {
    echo $row[0] . ' | ' . $row[1] . ' | ' . $row[2] . ' | ' . $row[3] . ' | ' . $row[4] . ' | ' . $row[5] . "\n";
}
?>