<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RODAGEST Full Dashboard Access Test ===\n\n";

// Using Laravel's session/tester approach to verify redirect targets
$testUsers = [
    ['email' => 'admin@rodagest.com', 'name' => 'Administrador', 'expectedRole' => 'admin'],
    ['email' => 'inspector@rodagest.com', 'name' => 'Inspector', 'expectedRole' => 'inspector'], 
    ['email' => 'operador@rodagest.com', 'name' => 'Operador/Chofer', 'expectedRole' => 'operador'],
    ['email' => 'limpieza@rodagest.com', 'name' => 'Limpieza', 'expectedRole' => 'limpieza'],
];

$allPassed = true;

foreach ($testUsers as $user) {
    echo "Testing {$user['email']} ({$user['name']})...\n";
    
    // Login
    \Illuminate\Support\Facades\Auth::logout();
    session()->flush();
    
    $result = \Illuminate\Support\Facades\Auth::attempt(['email' => $user['email'], 'password' => 'password123']);
    
    if (!$result) {
        echo "  ✗ LOGIN FAILED\n\n";
        $allPassed = false;
        continue;
    }
    
    $actualRole = \Illuminate\Support\Facades\Auth::user()->role;
    $roleOk = $actualRole === $user['expectedRole'];
    echo "  ✓ Login OK - Role: $actualRole\n";
    
    // Check what URLs are available/accessible based on role
    // We'll check by trying common dashboard-related routes
    $checks = [];
    
    // Check dashboard route
    $dashboardOk = @curl_status(http://127.0.0.1:8000/dashboard);
    $checks['dashboard'] = $dashboardOk;
    
    // Check role-specific routes
    $inspeccionesOk = false;
    $aseosOk = false;
    $unidadesOk = false;
    $quejasOk = false;
    
    if ($actualRole === 'admin' || $actualRole === 'inspector') {
        $inspeccionesOk = @curl_status(http://127.0.0.1:8000/inspecciones) === 200;
        $checks['inspecciones'] = $inspeccionesOk;
    }
    
    if ($actualRole === 'admin' || $actualRole === 'intendente' || $actualRole === 'limpieza') {
        $aseosOk = @curl_status(http://127.0.0.1:8000/aseos) === 200;
        $checks['aseos'] = $aseosOk;
    }
    
    if ($actualRole === 'admin') {
        $unidadesOk = @curl_status(http://127.0.0.1:8000/unidades) === 200;
        $quejasOk = @curl_status(http://127.0.0.1:8000/quejas) === 200;
        $checks['unidades'] = $unidadesOk;
        $checks['quejas'] = $quejasOk;
    }
    
    echo "  Routes status: ";
    foreach ($checks as $route => $status) {
        echo "$route=" . ($status ? '200' : 'error') . " ";
    }
    echo "\n";
    
    \Illuminate\Support\Facades\Auth::logout();
    echo "\n";
}

echo "===============================\n";
echo "Result: " . ($allPassed ? 'ALL TESTS PASSED' : 'SOME TESTS FAILED') . "\n";
echo "===============================\n";