#!/usr/bin/env powershell
# Script para crear la base de datos y usuarios MySQL
$sql = @"
CREATE DATABASE IF NOT EXISTS rodagest;
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON rodagest.* TO 'root'@'';
FLUSH PRIVILEGES;
"@

Write-Output "Ejecutando SQL MySQL..."
& mysql -u root -psecret -e $sql
Write-Output "Base de datos lista."