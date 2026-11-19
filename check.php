<?php
// Simple test - just verify the files are in place
echo "Verificando archivos modificados...\n";
echo "1. Login view: " . file_exists('C:\RODAGEST-main\resources\views\auth\login.blade.php') ? "EXISTS" : "MISSING" . "\n";
echo "2. Auth controller: " . file_exists('C:\RODAGEST-main\app\Http\Controllers\Auth\AuthenticatedSessionController.php') ? "EXISTS" : "MISSING" . "\n";
echo "3. Routes: " . file_exists('C:\RODAGEST-main\routes\web.php') ? "EXISTS" : "MISSING" . "\n";
echo "4. Navigation: " . file_exists('C:\RODAGEST-main\resources\views\layouts\navigation.blade.php') ? "EXISTS" : "MISSING" . "\n";
echo "5. Dashboard: " . file_exists('C:\RODAGEST-main\resources\views\dashboard.blade.php') ? "EXISTS" : "MISSING" . "\n";