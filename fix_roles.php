<?php
$pdo = new PDO('mysql:host=db;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Update existing users with roles not in the new enum
try {
    // Change supervisor to operador
    $pdo->exec("UPDATE users SET role = 'operador' WHERE role = 'supervisor'");
    echo "Actualizados: supervisor -> operador\n";
    
    // Change intendente to limpieza
    $pdo->exec("UPDATE users SET role = 'limpieza' WHERE role = 'intendente'");
    echo "Actualizados: intendente -> limpieza\n";
    
    // Verify
    $result = $pdo->query("SELECT id, role FROM users ORDER BY id");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . " | Role: '" . $row['role'] . "'\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>