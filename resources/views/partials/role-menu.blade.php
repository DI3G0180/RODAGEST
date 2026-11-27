@auth
    @php
        $user = auth()->user();
        $role = $user->role;
        $roleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'];
        $groupsByRole = [
            'admin' => [
                'Operación principal' => [['Panel principal', 'dashboard'], ['Unidades', 'unidades.index'], ['Inspecciones', 'inspecciones.index'], ['Aseos / Limpieza', 'aseos.index']],
                'Seguimiento y control' => [['Reportes operativos', 'reportes.index'], ['Quejas e incidencias', 'quejas.index'], ['Personal', 'empleados.index'], ['Papelera', 'papelera.index']],
            ],
            'inspector' => [
                'Operación principal' => [['Panel principal', 'dashboard'], ['Inspecciones', 'inspecciones.index']],
                'Seguimiento y control' => [['Quejas e incidencias', 'quejas.index']],
            ],
            'operador' => [
                'Operación principal' => [['Panel principal', 'dashboard'], ['Mi hoja de vida', 'empleados.hoja-vida.own']],
                'Seguimiento y control' => [['Mis reportes', 'quejas.index']],
            ],
            'limpieza' => [
                'Operación principal' => [['Panel principal', 'dashboard'], ['Aseos / Limpieza', 'aseos.index']],
                'Seguimiento y control' => [['Reportes de limpieza', 'quejas.index']],
            ],
        ];
        $navigationGroups = $groupsByRole[$role] ?? ['Operación principal' => [['Panel principal', 'dashboard']]];
        $navigationGroups['Cuenta y seguridad'] = [['Mi perfil', 'profile.edit'], ['Seguridad 2FA', '2fa.setup']];
        $desktopItems = collect($navigationGroups)->flatten(1);
        $desktopLabels = [
            'Panel principal' => 'Panel', 'Aseos / Limpieza' => 'Limpieza', 'Reportes operativos' => 'Reportes',
            'Quejas e incidencias' => 'Quejas', 'Personal' => 'Personal', 'Papelera' => 'Papelera',
            'Mi perfil' => 'Perfil', 'Seguridad 2FA' => 'Seguridad',
        ];
        $menuIcons = [
            'dashboard' => 'M3 10.5 12 3l9 7.5M5 9v11h5v-6h4v6h5V9',
            'unidades.index' => 'M3 16V6a2 2 0 0 1 2-2h11l5 5v7h-2m-14 0H3m4 0h8m-8 4a2 2 0 1 0 0-4 2 2 0 0 0 0 4m10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4M16 4v5h5',
            'inspecciones.index' => 'M8 4h8m-7-2h6a2 2 0 0 1 2 2v16H5V4a2 2 0 0 1 2-2m1 9 2 2 4-4m-6 8h6',
            'aseos.index' => 'M12 3v2m7 1-1.5 1.5M5 6l1.5 1.5M4 12h2m12 0h2M7 17a5 5 0 0 1 10 0m-12 4h14',
            'reportes.index' => 'M3 3v18h18M7 14h3v4H7zm7-6h3v10h-3zm-7 0h3v4H7z',
            'quejas.index' => 'M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-4-.99L3 21l1.99-5.5a8.4 8.4 0 0 1-.99-4A8.5 8.5 0 1 1 21 11.5Z',
            'empleados.index' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
            'papelera.index' => 'M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6',
            'empleados.hoja-vida.own' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
            'profile.edit' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8m-8 9a8 8 0 0 1 16 0',
            '2fa.setup' => 'M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Zm-3-11 2 2 4-4',
        ];
    @endphp

    <div class="fixed left-0 right-0 top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <header class="mx-auto flex h-16 max-w-[1600px] items-center justify-between gap-2 px-3 sm:px-4">
            <a href="{{ route('dashboard', [], false) }}" aria-label="RODAGEST, panel principal" class="flex min-w-0 shrink-0 items-center gap-2 lg:w-auto 2xl:w-44">
                <img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-9 w-auto max-w-40 object-contain [mix-blend-mode:multiply]">
                <span class="hidden truncate text-sm font-semibold text-[#083344] 2xl:inline">Gestión de Flotas</span>
            </a>

            <nav aria-label="Navegación principal" class="hidden min-w-0 flex-1 items-center justify-center gap-1.5 whitespace-nowrap px-1 lg:flex">
                @foreach($desktopItems as [$label, $routeName])
                    @php($active = request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                    <a href="{{ route($routeName, [], false) }}" title="{{ $label }}" @if($active) aria-current="page" @endif class="shrink-0 rounded-full px-1.5 py-1.5 text-xs font-semibold transition xl:px-2 xl:text-sm {{ $active ? 'bg-[#0E7490] text-white' : 'text-[#083344] hover:bg-cyan-50' }}">
                        <span>{{ $desktopLabels[$label] ?? $label }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="hidden shrink-0 items-center gap-2 lg:flex xl:gap-3">
                <div class="max-w-40 text-right"><b class="block truncate text-sm text-[#083344]">{{ $user->name }}</b><span class="text-[10px] font-bold uppercase tracking-wide text-[#0E7490]">{{ $roleLabels[$role] ?? $role }}</span></div>
                <form method="POST" action="{{ route('logout', [], false) }}">@csrf<button class="min-h-10 rounded-xl bg-[#083344] px-3 text-xs font-bold text-white transition hover:bg-[#0E7490] xl:px-4 xl:text-sm">Salir</button></form>
            </div>

            <button type="button" onclick="window.toggleMobileMenu(true)" aria-label="Abrir menú de navegación" aria-controls="mobile-nav-drawer" aria-expanded="false" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#083344] p-2.5 text-white transition hover:bg-[#0E7490] focus:outline-none focus:ring-2 focus:ring-[#0E7490] lg:hidden">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </header>
    </div>

@endauth
