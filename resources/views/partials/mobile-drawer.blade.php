@auth
    @php
        $mobileUser = auth()->user();
        $mobileRole = $mobileUser->role;
        $mobileRoleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'];
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
                <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h2 id="mobile-nav-title" class="break-words text-lg font-extrabold text-white">{{ $mobileUser->name }}</h2><span class="mt-1 inline-flex rounded-full bg-[#0E7490] px-3 py-1 text-xs font-bold uppercase text-white">{{ $mobileRoleLabels[$mobileRole] ?? $mobileRole }}</span></div><button type="button" data-mobile-menu-close onclick="window.toggleMobileMenu(false)" class="inline-flex min-h-11 shrink-0 items-center rounded-lg bg-white px-3 text-sm font-bold text-[#083344]">✕ Cerrar</button></div>
            </div>
            <div style="flex:1 1 auto; overflow-y:auto; padding:12px 0;">
                @foreach($mobileGroups as $groupLabel => $items)
                    <section class="border-b border-slate-100 py-2"><h3 class="px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $groupLabel }}</h3>
                        @foreach($items as [$label,$routeName])
                            @php($mobileActive = request()->routeIs($routeName) || request()->routeIs($routeName.'.*'))
                            <a href="{{ route($routeName, [], false) }}" @if($mobileActive) aria-current="page" @endif onclick="window.toggleMobileMenu(false)" class="block border-l-4 px-4 py-3 text-base font-medium {{ $mobileActive ? 'border-[#0E7490] bg-cyan-50 font-bold text-[#0E7490]' : 'border-transparent text-[#083344] hover:bg-slate-100' }}">{{ $label }}</a>
                        @endforeach
                    </section>
                @endforeach
            </div>
            <form method="POST" action="{{ route('logout', [], false) }}" class="shrink-0 border-t border-slate-200 bg-white p-4">@csrf<button class="min-h-12 w-full rounded-xl bg-[#083344] px-4 py-3 text-base font-bold text-white">Cerrar sesión</button></form>
        </aside>
    </div>
@endauth
