@auth
    @php
        $user = auth()->user();
        $role = $user->role;
        $roleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'];
        $groupsByRole = [
            'admin' => [
                'Operación principal' => [['Panel principal', 'dashboard'], ['Unidades', 'unidades.index']],
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
        $mobileShortcuts = match ($role) {
            'admin' => [['Inicio', 'dashboard', ''], ['Unidades', 'unidades.index', ''], ['Reportes', 'reportes.index', '']],
            'inspector' => [['Inicio', 'dashboard', ''], ['Inspección', 'inspecciones.create', ''], ['Historial', 'inspecciones.index', '']],
            'operador' => [['Inicio', 'dashboard', ''], ['Turno', 'dashboard', '#turno-operador'], ['Reportes', 'quejas.index', '']],
            'limpieza' => [['Inicio', 'dashboard', ''], ['Aseo rápido', 'dashboard', '#aseo-rapido'], ['Aseos', 'aseos.index', '']],
            default => [['Inicio', 'dashboard', '']],
        };
        $desktopLabels = ['Panel principal' => 'Panel', 'Reportes operativos' => 'Reportes', 'Quejas e incidencias' => 'Quejas', 'Seguridad 2FA' => '2FA'];
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

    <div x-data="{ open: false, toggle() { this.open = !this.open }, close() { this.open = false } }" x-effect="document.body.style.overflow = open ? 'hidden' : ''" @keydown.escape.window="close()">
    <div class="sticky top-0 z-30 border-b border-[#6DA5C0]/50 bg-white shadow-sm">
        <header class="mx-auto flex h-16 max-w-[1600px] items-center justify-between gap-3 px-4">
            <a href="{{ route('dashboard', [], false) }}" aria-label="RODAGEST, panel principal" class="flex min-w-0 shrink-0 items-center gap-2 md:w-44">
                <img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-9 w-auto max-w-40 object-contain [mix-blend-mode:multiply]">
                <span class="hidden truncate text-sm font-semibold text-[#294D61] lg:inline">Gestión de Flotas</span>
            </a>

            <nav aria-label="Navegación principal" class="hidden min-w-0 flex-1 items-center justify-center gap-1 overflow-x-auto whitespace-nowrap px-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden md:flex">
                @foreach($desktopItems as [$label, $routeName])
                    @php($active = request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                    <a href="{{ route($routeName, [], false) }}" title="{{ $label }}" @if($active) aria-current="page" @endif class="shrink-0 rounded-full px-2.5 py-2 text-xs font-semibold transition lg:px-3 lg:text-sm {{ $active ? 'bg-[#0C7075] text-white' : 'text-[#05161A] hover:bg-[#6DA5C0]/20' }}">
                        <span class="lg:hidden">{{ $desktopLabels[$label] ?? $label }}</span><span class="hidden lg:inline">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="hidden shrink-0 items-center gap-3 md:flex">
                <div class="max-w-40 text-right"><b class="block truncate text-sm text-[#05161A]">{{ $user->name }}</b><span class="text-[10px] font-bold uppercase tracking-wide text-[#0C7075]">{{ $roleLabels[$role] ?? $role }}</span></div>
                <form method="POST" action="{{ route('logout', [], false) }}">@csrf<button class="min-h-10 rounded-xl bg-[#072E33] px-4 text-sm font-bold text-white transition hover:bg-[#05161A]">Salir</button></form>
            </div>

            <button type="button" @click="toggle()" :aria-expanded="open.toString()" aria-label="Abrir menú de navegación" aria-controls="rodagest-mobile-drawer" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#072E33] p-2.5 text-white transition hover:bg-[#05161A] focus:outline-none focus:ring-2 focus:ring-[#6DA5C0] md:hidden">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </header>
    </div>

    <template x-teleport="body">
    <div id="rodagest-mobile-drawer" x-show="open" x-cloak x-transition.opacity :aria-hidden="(!open).toString()" class="fixed inset-0 z-40 md:hidden">
        <div @click="close()" class="absolute inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"></div>

        <aside id="rodagest-mobile-panel" role="dialog" aria-modal="true" aria-labelledby="rodagest-drawer-title" tabindex="-1" class="fixed inset-y-0 right-0 z-50 flex h-[100dvh] w-72 max-w-[85vw] flex-col overflow-y-auto bg-white text-slate-800 shadow-2xl">
            <div class="min-h-0 flex-1">
                <div class="flex items-start justify-between gap-3 border-b border-slate-200 bg-white p-4">
                    <div class="flex items-start gap-3">
                        <img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-9 w-auto object-contain [mix-blend-mode:multiply]">
                        <div class="min-w-0"><p id="rodagest-drawer-title" class="break-words font-extrabold text-slate-900">Hola, {{ $user->name }}</p><span class="mt-1 inline-flex rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-bold uppercase text-teal-900">{{ $roleLabels[$role] ?? $role }}</span><p class="mt-2 text-xs font-medium text-slate-600">Explorar RODAGEST</p></div>
                    </div>
                    <button type="button" @click="close()" aria-label="Cerrar menú" class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-slate-100 text-xl font-bold text-slate-800 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-700">×</button>
                </div>

                <nav aria-label="Menú móvil" class="divide-y divide-slate-200 bg-white">
                    @foreach($navigationGroups as $groupLabel => $items)
                        <section class="py-2">
                            <h2 class="px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $groupLabel }}</h2>
                            @foreach($items as [$label, $routeName])
                                @php($active = request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                                <a href="{{ route($routeName, [], false) }}" @if($active) aria-current="page" @endif @click="close()" class="flex min-h-12 items-center gap-3 border-l-4 px-4 py-3 text-sm {{ $active ? 'border-teal-700 bg-teal-50 font-bold text-teal-900' : 'border-transparent font-medium text-slate-800 hover:bg-teal-50' }}">
                                    <svg aria-hidden="true" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="{{ $menuIcons[$routeName] ?? $menuIcons['dashboard'] }}"/></svg><span>{{ $label }}</span>
                                </a>
                            @endforeach
                        </section>
                    @endforeach
                </nav>
            </div>

            <form method="POST" action="{{ route('logout', [], false) }}" class="shrink-0 border-t border-slate-200 bg-white p-4">@csrf<button class="w-full rounded-xl bg-[#072E33] py-3 font-bold text-white transition hover:bg-[#05161A]">Cerrar sesión</button></form>
        </aside>
    </div>
    </template>

    <nav aria-label="Accesos rápidos" class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-4 border-t border-slate-200 bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_16px_rgba(15,23,42,.08)] md:hidden">
        @foreach($mobileShortcuts as [$label, $routeName, $anchor])
            <a href="{{ route($routeName, [], false) }}{{ $anchor ?? '' }}" @click="close()" class="flex min-h-14 flex-col items-center justify-center gap-0.5 px-1 py-2 text-center text-[11px] font-semibold text-slate-700 hover:bg-teal-50 hover:text-teal-900">
                <span class="max-w-full truncate">{{ $label }}</span>
            </a>
        @endforeach
        <button type="button" @click="toggle()" :aria-expanded="open.toString()" aria-controls="rodagest-mobile-drawer" class="flex min-h-14 flex-col items-center justify-center gap-0.5 px-1 py-2 text-[11px] font-bold text-slate-800 hover:bg-teal-50">
            <span aria-hidden="true" class="text-lg leading-none">☰</span><span>Menú</span>
        </button>
    </nav>
    </div>
@endauth
