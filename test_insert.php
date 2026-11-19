<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, name, password, role) VALUES (:email, :name, :password, :role)");
    $stmt->execute([
        ':email' => 'supervisor@test.com',
        ':name' => 'Supervisor Test',
        ':password' => '$2y$12$testhash',
        ':role' => 'supervisor'
    ]);
    echo "Éxito insertando supervisor\n";
} catch (PDOException $e) {
    echo "Error insertando supervisor: " . $e->getMessage() . "\n";
}
?>