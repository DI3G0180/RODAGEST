<?php
$mysqli = new mysqli('db', 'root', 'secret', 'rodagest_db');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}
$sql = 'ALTER TABLE users MODIFY COLUMN role ENUM("admin", "supervisor", "inspector", "intendente") NOT NULL DEFAULT "operador"';
if ($mysqli->query($sql) === TRUE) {
    echo "Success: Enum updated\n";
} else {
    echo "Error: " . $mysqli->error . "\n";
}
$mysqli->close();