@auth
    @php
        $mobileUser = auth()->user();
        $mobileRole = $mobileUser->role;
        $mobileRoleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'];
        $mobileIcons = [
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
        $mobileGroupsByRole = [
            'admin' => ['Operación principal' => [['Panel principal','dashboard'],['Unidades','unidades.index'],['Inspecciones','inspecciones.index'],['Aseos / Limpieza','aseos.index']], 'Seguimiento y control' => [['Reportes operativos','reportes.index'],['Quejas e incidencias','quejas.index'],['Personal','empleados.index'],['Papelera','papelera.index']]],
            'inspector' => ['Operación principal' => [['Panel principal','dashboard'],['Inspecciones','inspecciones.index']], 'Seguimiento y control' => [['Quejas e incidencias','quejas.index']]],
            'operador' => ['Operación principal' => [['Panel principal','dashboard'],['Mi hoja de vida','empleados.hoja-vida.own']], 'Seguimiento y control' => [['Mis reportes','quejas.index']]],
            'limpieza' => ['Operación principal' => [['Panel principal','dashboard'],['Aseos / Limpieza','aseos.index']], 'Seguimiento y control' => [['Reportes de limpieza','quejas.index']]],
        ];
        $mobileGroups = $mobileGroupsByRole[$mobileRole] ?? ['Operación principal' => [['Panel principal','dashboard']]];
        $mobileGroups['Cuenta y seguridad'] = [['Mi perfil','profile.edit'],['Seguridad 2FA','2fa.setup']];
    @endphp
    <div id="mobile-nav-drawer" class="lg:hidden" aria-hidden="true" style="display: none; position: fixed; inset: 0; z-index: 99999;">
        <div data-mobile-menu-close onclick="window.toggleMobileMenu(false)" aria-label="Cerrar menú" style="position: fixed; inset: 0; background: rgba(8, 51, 68, 0.65); z-index: 1;"></div>
        <aside role="dialog" aria-modal="true" aria-labelledby="mobile-nav-title" style="position:fixed; top:0; right:0; bottom:0; width:280px; max-width:85vw; background:#ffffff; z-index:2; overflow-y:auto; box-shadow:-12px 0 32px rgba(0,0,0,.28); display:flex; flex-direction:column;">
            <div style="background: linear-gradient(120deg, #083344, #0E7490); color:#fff; padding:20px;">
                <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h2 id="mobile-nav-title" class="break-words text-lg font-extrabold text-white">{{ $mobileUser->name }}</h2><span class="mt-1 inline-flex rounded-full bg-[#0E7490] px-3 py-1 text-xs font-bold uppercase text-white">{{ $mobileRoleLabels[$mobileRole] ?? $mobileRole }}</span></div><button type="button" data-mobile-menu-close onclick="window.toggleMobileMenu(false)" aria-label="Cerrar menú" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 text-lg font-bold text-white transition hover:bg-white/25">✕</button></div>
            </div>
            <div style="flex:1 1 auto; overflow-y:auto; padding:12px 0;">
                @foreach($mobileGroups as $groupLabel => $items)
                    <section class="border-b border-slate-100 py-2"><h3 class="px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $groupLabel }}</h3>
                        @foreach($items as [$label,$routeName])
                            @php($mobileActive = request()->routeIs($routeName) || request()->routeIs($routeName.'.*'))
                            <a href="{{ route($routeName, [], false) }}" @if($mobileActive) aria-current="page" @endif onclick="window.toggleMobileMenu(false)" class="flex items-center gap-3 border-l-4 px-4 py-3 text-base font-medium {{ $mobileActive ? 'border-[#0E7490] bg-cyan-50 font-bold text-[#0E7490]' : 'border-transparent text-[#083344] hover:bg-slate-100' }}"><span aria-hidden="true" class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#0E7490]/10 text-[#0E7490]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $mobileIcons[$routeName] ?? 'M12 5v14m-7-7h14' }}"/></svg></span><span>{{ $label }}</span></a>
                        @endforeach
                    </section>
                @endforeach
            </div>
            <form method="POST" action="{{ route('logout', [], false) }}" class="shrink-0 border-t border-slate-200 bg-white p-4">@csrf<button class="min-h-12 w-full rounded-xl bg-[#083344] px-4 py-3 text-base font-bold text-white">Cerrar sesión</button></form>
        </aside>
    </div>
@endauth
