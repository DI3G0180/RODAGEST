<?php
require_once __DIR__.'/../app/vendor/autoload.php';
$emails = ['admin@rodagest.com','inspector@rodagest.com','operador@rodagest.com','limpieza@rodagest.com'];
foreach($emails as $e) {
    $u = App\Models\User::where('email',$e)->first();
    if($u) {
        $password_ok = password_verify('password123', $u->password);
        echo $u->email.' -> role: '.$u->role.' -> exists: yes -> password password123 ok: '.($password_ok?'yes':'no')."\n";
    } else {
        echo $e.' -> not found\n';
    }
}