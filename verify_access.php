<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RODAGEST Login & Dashboard Access Test ===\n\n";

$testUsers = [
    ['email' => 'admin@rodagest.com', 'name' => 'Administrador', 'expectedRole' => 'admin', 'expectedPath' => '/dashboard'],
    ['email' => 'inspector@rodagest.com', 'name' => 'Inspector', 'expectedRole' => 'inspector', 'expectedPath' => '/inspecciones'],
    ['email' => 'operador@rodagest.com', 'name' => 'Operador/Chofer', 'expectedRole' => 'operador', 'expectedPath' => '/aseos'],
    ['email' => 'limpieza@rodagest.com', 'name' => 'Limpieza', 'expectedRole' => 'limpieza', 'expectedPath' => '/aseos'],
];

$allPassed = true;

foreach ($testUsers as $user) {
    echo "Testing {$user['email']} ({$user['name']})...\n";
    
    // Logout any existing session
    \Illuminate\Support\Facades\Auth::logout();
    session()->flush();
    
    // Attempt login
    $result = \Illuminate\Support\Facades\Auth::attempt(['email' => $user['email'], 'password' => 'password123']);
    
    if (!$result) {
        echo "  ✗ LOGIN FAILED\n\n";
        $allPassed = false;
        continue;
    }
    
    // Check role
    $actualRole = \Illuminate\Support\Facades\Auth::user()->role;
    $roleOk = $actualRole === $user['expectedRole'];
    echo "  Role: actual=$actualRole, expected={$user['expectedRole']} -> " . ($roleOk ? '✓' : '✗') . "\n";
    
    if (!$roleOk) {
        $allPassed = false;
        \Illuminate\Support\Facades\Auth::logout();
        continue;
    }
    
    // Check the dashboard redirect by checking the intended URL or session
    $intended = session()->get('intended', 'none');
    echo "  Intended URL: $intended\n\n";
    
    \Illuminate\Support\Facades\Auth::logout();
}

echo "===============================\n";
echo "Result: " . ($allPassed ? 'ALL TESTS PASSED' : 'SOME TESTS FAILED') . "\n";
echo "===============================\n";