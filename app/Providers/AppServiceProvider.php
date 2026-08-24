<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Definir Gates por Rol
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('isInspector', function (User $user) {
            return in_array($user->role, ['admin', 'inspector']);
        });

        Gate::define('isOperador', function (User $user) {
            return in_array($user->role, ['admin', 'operador']);
        });
    }
}