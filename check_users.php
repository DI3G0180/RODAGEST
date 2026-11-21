<?php
require_once __DIR__.'/../vendor/autoload.php';
$emails = ['admin@rodagest.com','inspector@rodagest.com','operador@rodagest.com','limpieza@rodagest.com'];
foreach($emails as $e) {
    $u = App\Models\User::where('email',$e)->first();
    echo $u ? $u->email." -> role: ".$u->role." -> exists: yes" : "not found";
    echo "\n";
}