@auth
    @php
        $role = auth()->user()->role;
        $itemsByRole = [
            'admin' => [['Panel principal','dashboard'],['Personal','empleados.index'],['Unidades','unidades.index'],['Reportes operativos','reportes.index'],['Quejas e incidencias','quejas.index'],['Papelera','papelera.index']],
            'inspector' => [['Panel principal','dashboard'],['Inspecciones','inspecciones.index'],['Quejas e incidencias','quejas.index']],
            'operador' => [['Panel principal','dashboard'],['Mi hoja de vida','empleados.hoja-vida.own'],['Mis reportes','quejas.index']],
            'limpieza' => [['Panel principal','dashboard'],['Aseo de unidades','aseos.index'],['Reportes de limpieza','quejas.index']],
        ];
        $menuItems = $itemsByRole[$role] ?? [['Panel principal','dashboard']];
        $menuItems[] = ['Seguridad 2FA','2fa.setup'];
    @endphp
    <div x-data="{open:false}" @keydown.escape.window="open=false" class="sticky top-0 z-50 border-b border-[#B1D0E2] bg-[#FBF1E5]/95 shadow-sm backdrop-blur">
        <header class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button type="button" @click="open=true" aria-label="Abrir menú" aria-controls="role-navigation" :aria-expanded="open.toString()" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-[#B1D0E2] bg-white text-[#018FC7] hover:bg-[#DBE7F3] focus:outline-none focus:ring-2 focus:ring-[#018FC7]">
                    <svg aria-hidden="true" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('dashboard', [], false) }}" class="flex min-w-0 items-center gap-2"><img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-9 w-auto max-w-28 object-contain [mix-blend-mode:multiply]"><span class="hidden truncate font-bold text-[#018FC7] sm:inline">Gestión de flotas</span></a>
            </div>
            <div class="min-w-0 text-right"><b class="block max-w-[44vw] truncate text-sm">{{ auth()->user()->name }}</b><small class="text-xs uppercase text-[#018FC7]">{{ $role }}</small></div>
        </header>
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[60] bg-slate-950/40" @click="open=false" aria-hidden="true"></div>
        <aside id="role-navigation" x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-[70] flex w-[min(21rem,88vw)] flex-col border-r border-[#B1D0E2] bg-[#FBF1E5] p-4 shadow-2xl" @click.stop>
            <div class="flex items-center justify-between gap-3 border-b border-[#B1D0E2] pb-4"><div class="min-w-0"><p class="truncate font-bold">{{ auth()->user()->name }}</p><p class="text-xs uppercase tracking-wide text-[#018FC7]">{{ $role }}</p></div><button type="button" @click="open=false" aria-label="Cerrar menú" class="grid h-10 w-10 place-items-center rounded-lg border border-[#B1D0E2] bg-white text-slate-700">✕</button></div>
            <nav aria-label="Navegación principal" class="mt-4 flex-1 space-y-1 overflow-y-auto" @click="open=false">
                @foreach($menuItems as [$label,$routeName])
                    @php($active = $routeName === 'dashboard' ? request()->routeIs('dashboard') : request()->routeIs($routeName) || request()->routeIs($routeName . '.*'))
                    <a href="{{ route($routeName, [], false) }}" @if($active) aria-current="page" @endif class="flex min-h-12 items-center rounded-xl px-4 text-sm font-semibold {{ $active ? 'bg-[#DBE7F3] text-[#018FC7]' : 'text-slate-700 hover:bg-[#DBE7F3] hover:text-[#018FC7]' }}">{{ $label }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout', [], false) }}" class="border-t border-[#B1D0E2] pt-4">@csrf<button class="min-h-12 w-full rounded-xl border border-[#B1D0E2] bg-white px-4 text-left font-semibold text-[#018FC7] hover:bg-[#DBE7F3]">Salir</button></form>
        </aside>
    </div>
@endauth
