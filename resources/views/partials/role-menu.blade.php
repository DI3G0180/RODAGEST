@auth
    @php
        $role = auth()->user()->role;
        $itemsByRole = [
            'admin' => [
                ['label' => 'Panel principal', 'route' => 'dashboard'],
                ['label' => 'Personal', 'route' => 'empleados.index'],
                ['label' => 'Unidades', 'route' => 'unidades.index'],
                ['label' => 'Reportes operativos', 'route' => 'reportes.index'],
                ['label' => 'Quejas e incidencias', 'route' => 'quejas.index'],
            ],
            'inspector' => [
                ['label' => 'Panel principal', 'route' => 'dashboard'],
                ['label' => 'Inspecciones', 'route' => 'inspecciones.index'],
                ['label' => 'Quejas e incidencias', 'route' => 'quejas.index'],
            ],
            'operador' => [
                ['label' => 'Panel principal', 'route' => 'dashboard'],
                ['label' => 'Mi hoja de vida', 'route' => 'empleados.hoja-vida.own'],
                ['label' => 'Mis reportes', 'route' => 'quejas.index'],
            ],
            'limpieza' => [
                ['label' => 'Panel principal', 'route' => 'dashboard'],
                ['label' => 'Aseo de unidades', 'route' => 'aseos.index'],
                ['label' => 'Reportes de limpieza', 'route' => 'quejas.index'],
            ],
        ];
        $menuItems = $itemsByRole[$role] ?? [['label' => 'Panel principal', 'route' => 'dashboard']];
        $menuItems[] = ['label' => 'Seguridad 2FA', 'route' => '2fa.setup'];
    @endphp
    <nav aria-label="Navegación principal" class="mx-auto grid w-full max-w-7xl grid-cols-2 gap-2 px-4 pb-3 sm:flex sm:flex-wrap sm:px-6">
        @foreach($menuItems as $item)
            @php($active = $item['route'] === 'dashboard' ? request()->routeIs('dashboard') : request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'))
            <a href="{{ route($item['route'], [], false) }}" @if($active) aria-current="page" @endif class="inline-flex min-h-10 min-w-0 items-center justify-center rounded-lg px-3 py-2 text-center text-sm font-semibold transition {{ $active ? 'bg-[#DBE7F3] text-[#018FC7]' : 'text-slate-700 hover:bg-[#DBE7F3] hover:text-[#018FC7]' }}">
                <span class="break-words">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
@endauth
