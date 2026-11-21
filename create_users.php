#!/usr/bin/env php
<?php
// Script para crear los 4 usuarios con password password123
$host = 'localhost';
$db = 'rodagest';
$user = 'root';
$pass = 'secret';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Los 4 usuarios con sus correos y roles
$users = [
    ['email' => 'admin@rodagest.com', 'role' => 'admin', 'name' => 'Administrador RODAGEST'],
    ['email' => 'inspector@rodagest.com', 'role' => 'inspector', 'name' => 'Inspector RODAGEST'],
    ['email' => 'operador@rodagest.com', 'role' => 'operador', 'name' => 'Operador RODAGEST'],
    ['email' => 'limpieza@rodagest.com', 'role' => 'limpieza', 'name' => 'Personal Limpieza RODAGEST'],
];

$password = 'password123'; // Plain text - bcrypt will hash it

foreach ($users as $u) {
    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $u['email']]);
    
    if ($stmt->fetch()) {
        echo "Usuario ya existe: {$u['email']}\n";
        continue;
    }
    
    // Insertar usuario con password hasheado
    $sql = "INSERT INTO users (name, email, role, password, created_at, updated_at) 
            VALUES (:name, :email, :role, :password, NOW(), NOW())";
    $stmt = $pdo->prepare($sql);
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
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