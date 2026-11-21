#!/usr/bin/env php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=rodagest_db', 'root', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Users with password password123 hashed
$users = [
    ['email' => 'admin@rodagest.com', 'role' => 'admin', 'name' => 'Administrador RODAGEST'],
    ['email' => 'inspector@rodagest.com', 'role' => 'inspector', 'name' => 'Inspector RODAGEST'],
    ['email' => 'operador@rodagest.com', 'role' => 'operador', 'name' => 'Operador RODAGEST'],
    ['email' => 'limpieza@rodagest.com', 'role' => 'limpieza', 'name' => 'Personal Limpieza RODAGEST'],
];

$password = 'password123';
$hashed = password_hash($password, PASSWORD_DEFAULT);

foreach ($users as $u) {
    // Check if user exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $u['email']]);
    
    if ($stmt->fetch()) {
        echo "Usuario ya existe: {$u['email']}\n";
        continue;
    }
    
    // Insert user
    $sql = "INSERT INTO users (name, email, role, password, created_at, updated_at) 
            VALUES (:name, :email, :role, :password, NOW(), NOW())";
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $u['role'],
        'password' => $hashed,
    ]);
    
    if ($result) {
        echo "Creado: {$u['email']} - role: {$u['role']}\n";
    } else {
        echo "Error al crear: {$u['email']}\n";
    }
}

echo "\nTodos los usuarios configurados con password: $password\n";