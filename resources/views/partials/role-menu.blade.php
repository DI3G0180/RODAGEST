@auth
    @php
        $role = auth()->user()->role;
        $roleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'];
        $itemsByRole = [
            'admin' => [['Panel principal','dashboard'],['Personal','empleados.index'],['Unidades','unidades.index'],['Reportes operativos','reportes.index'],['Quejas e incidencias','quejas.index'],['Papelera','papelera.index']],
            'inspector' => [['Panel principal','dashboard'],['Inspecciones','inspecciones.index'],['Quejas e incidencias','quejas.index']],
            'operador' => [['Panel principal','dashboard'],['Mi hoja de vida','empleados.hoja-vida.own'],['Mis reportes','quejas.index']],
            'limpieza' => [['Panel principal','dashboard'],['Aseo de unidades','aseos.index'],['Reportes de limpieza','quejas.index']],
        ];
        $menuItems = $itemsByRole[$role] ?? [['Panel principal','dashboard']];
        $menuItems[] = ['Seguridad 2FA','2fa.setup'];
        $desktopLabels = ['Panel principal'=>'Panel', 'Reportes operativos'=>'Reportes', 'Quejas e incidencias'=>'Quejas', 'Seguridad 2FA'=>'2FA'];
        $menuIcons = [
            'dashboard' => 'M3 10.5 12 3l9 7.5M5 9v11h5v-6h4v6h5V9',
            'empleados.index' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8m13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75',
            'unidades.index' => 'M3 16V6a2 2 0 0 1 2-2h11l5 5v7h-2m-14 0H3m4 0h8m-8 4a2 2 0 1 0 0-4 2 2 0 0 0 0 4m10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4M16 4v5h5',
            'reportes.index' => 'M3 3v18h18M7 14h3v4H7zm7-6h3v10h-3zm-7 0h3v4H7z',
            'quejas.index' => 'M21 11.5a8.5 8.5 0 0 1-8.5 8.5 8.4 8.4 0 0 1-4-.99L3 21l1.99-5.5a8.4 8.4 0 0 1-.99-4A8.5 8.5 0 1 1 21 11.5Z',
            'papelera.index' => 'M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6',
            'inspecciones.index' => 'M8 4h8m-7-2h6a2 2 0 0 1 2 2v16H5V4a2 2 0 0 1 2-2m1 9 2 2 4-4m-6 8h6',
            'aseos.index' => 'M12 3v2m7 1-1.5 1.5M5 6l1.5 1.5M4 12h2m12 0h2M7 17a5 5 0 0 1 10 0m-12 4h14',
            'empleados.hoja-vida.own' => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
            '2fa.setup' => 'M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Zm-3-11 2 2 4-4',
        ];
    @endphp
    <div x-data="{open:false}" @keydown.escape.window="open=false" class="sticky top-0 z-40 border-b border-[#7FB3B8] bg-white/95 shadow-sm backdrop-blur">
        <header class="mx-auto hidden min-h-[4.5rem] max-w-[1600px] items-center gap-3 px-4 md:flex">
            <a href="{{ route('dashboard', [], false) }}" class="flex w-32 shrink-0 items-center gap-2 lg:w-48"><img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-10 w-12 object-contain [mix-blend-mode:multiply]"><span class="hidden truncate text-sm font-semibold text-slate-600 lg:inline">Gestión de Flotas</span></a>
            <nav aria-label="Navegación principal" class="flex min-w-0 flex-1 items-center justify-center gap-0.5 overflow-x-auto whitespace-nowrap px-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden lg:gap-1">
                @foreach($menuItems as [$label,$routeName])
                    @php($active = $routeName === 'dashboard' ? request()->routeIs('dashboard') : request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                    <a href="{{ route($routeName, [], false) }}" title="{{ $label }}" @if($active) aria-current="page" @endif class="shrink-0 rounded-full px-2 py-2 text-[11px] font-medium transition lg:px-3.5 lg:text-sm {{ $active ? 'bg-[#2E6F73] text-white shadow-sm' : 'text-[#0F3D3E] hover:bg-[#EADCC8]' }}"><span class="xl:hidden">{{ $desktopLabels[$label] ?? $label }}</span><span class="hidden xl:inline">{{ $label }}</span></a>
                @endforeach
            </nav>
            <div class="flex w-40 shrink-0 items-center justify-end gap-2 lg:w-48"><div class="min-w-0 text-right"><b class="block truncate text-xs text-[#0A0F14] lg:text-sm">{{ auth()->user()->name }}</b><small class="text-[10px] uppercase tracking-wide text-[#2E6F73]">{{ $roleLabels[$role] ?? $role }}</small></div><form method="POST" action="{{ route('logout', [], false) }}">@csrf<button class="min-h-10 shrink-0 rounded-full border border-[#7FB3B8] px-3 text-sm font-semibold text-[#2E6F73] transition hover:bg-[#EADCC8]">Salir</button></form></div>
        </header>

        <header class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 bg-[#0A0F14] px-4 md:hidden">
            <a href="{{ route('dashboard', [], false) }}" aria-label="RODAGEST, ir al panel" class="flex min-w-0 items-center"><img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-10 w-auto max-w-40 shrink-0 object-contain brightness-0 invert"></a>
            <button type="button" @click="open=true" aria-label="Abrir menú" aria-controls="role-navigation" :aria-expanded="open.toString()" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-white/20 bg-white/5 text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#7FB3B8]"><svg aria-hidden="true" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        </header>

        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 md:hidden" @click="open=false" aria-hidden="true"></div>
        <aside id="role-navigation" x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" role="dialog" aria-modal="true" aria-label="Menú de navegación" class="fixed inset-y-0 left-0 z-50 flex h-full w-72 max-w-[85vw] flex-col justify-between border-r border-[#7FB3B8] bg-[#EADCC8] shadow-2xl md:hidden" @click.stop>
            <div class="flex min-h-0 flex-1 flex-col">
                <div class="flex items-center justify-between gap-3 border-b border-[#7FB3B8] p-4"><div class="flex min-w-0 items-center gap-3"><img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-11 w-12 shrink-0 object-contain [mix-blend-mode:multiply]"><div class="min-w-0"><p class="truncate font-bold text-[#0A0F14]">{{ auth()->user()->name }}</p><p class="text-xs font-semibold uppercase tracking-wide text-[#2E6F73]">{{ $roleLabels[$role] ?? $role }}</p></div></div><button type="button" @click="open=false" aria-label="Cerrar menú" class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-[#7FB3B8] bg-white text-[#0F3D3E] hover:bg-[#EADCC8]">✕</button></div>
                <nav aria-label="Navegación principal" class="mt-3 flex-1 space-y-1 overflow-y-auto p-3" @click="open=false">
                    @foreach($menuItems as [$label,$routeName])
                        @php($active = $routeName === 'dashboard' ? request()->routeIs('dashboard') : request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                        <a href="{{ route($routeName, [], false) }}" @if($active) aria-current="page" @endif class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium {{ $active ? 'bg-[#2E6F73] text-white shadow-sm' : 'text-[#0A0F14] hover:bg-white/70 hover:text-[#0F3D3E]' }}"><svg aria-hidden="true" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" viewBox="0 0 24 24"><path d="{{ $menuIcons[$routeName] ?? $menuIcons['dashboard'] }}"/></svg><span>{{ $label }}</span></a>
                    @endforeach
                </nav>
            </div>
            <form method="POST" action="{{ route('logout', [], false) }}" class="border-t border-[#7FB3B8] p-4">@csrf<button class="min-h-12 w-full rounded-xl bg-[#C6601F] px-4 text-left font-semibold text-white shadow-sm hover:bg-[#A84D17]">Cerrar sesión</button></form>
        </aside>
    </div>
@endauth
