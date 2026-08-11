<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light only">

    <title>{{ config('app.name', 'RodaGest') }} - @yield('title', 'Control')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" />

    <!-- Scripts / Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 min-h-screen text-slate-800">
    <div class="flex min-h-screen">

        <!-- SIDEBAR / MENÚ LATERAL OSCURO (FORZADO) -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between shrink-0 shadow-xl border-r border-slate-800" style="background-color: #0f172a !important;">
            <div>
                <!-- LOGO Y TÍTULO DE LA APP -->
                <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 text-white font-bold text-xl">
                        <i class="ri-bus-fill"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg tracking-wide text-white" style="color: #ffffff !important;">RodaGest</h1>
                        <p class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Equipo Rodante</p>
                    </div>
                </div>

                <!-- NAVEGACIÓN PRINCIPAL -->
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    <div class="px-3 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menú Principal</div>

                    <!-- BOTÓN ASEOS -->
                    <a href="{{ route('aseos.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('aseos.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                       style="{{ request()->routeIs('aseos.*') ? 'background-color: #2563eb !important; color: #ffffff !important;' : 'color: #cbd5e1 !important;' }}">
                        <i class="ri-clean-hand-line text-lg"></i>
                        <span>Aseo de Unidades</span>
                    </a>

                    <!-- BOTÓN INSPECCIÓN DE DAÑOS -->
                    <a href="{{ route('inspecciones.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('inspecciones.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                       style="{{ request()->routeIs('inspecciones.*') ? 'background-color: #2563eb !important; color: #ffffff !important;' : 'color: #cbd5e1 !important;' }}">
                        <i class="ri-shield-cross-line text-lg"></i>
                        <span>Inspección de Daños</span>
                    </a>

                    <!-- BOTÓN CATÁLOGO DE UNIDADES -->
                    <a href="{{ route('unidades.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('unidades.*') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                       style="{{ request()->routeIs('unidades.*') ? 'background-color: #2563eb !important; color: #ffffff !important;' : 'color: #cbd5e1 !important;' }}">
                        <i class="ri-bus-2-line text-lg"></i>
                        <span>Catálogo de Unidades</span>
                    </a>
                </nav>
            </div>

            <!-- FOOTER DEL SIDEBAR / SOLO INFO, SIN CLIC -->
            <div class="p-3 border-t border-slate-800 bg-slate-950 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs text-white shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate" style="color: #ffffff !important;">{{ Auth::user()->name ?? 'Usuario' }}</p>
                    <p class="text-[9px] text-blue-400 uppercase font-bold tracking-wider truncate">
                        {{ Auth::user()->role ?? 'operador' }}
                    </p>
                </div>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL DE LA PÁGINA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- HEADER SUPERIOR -->
            <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">
                    @yield('title', 'RodaGest')
                </h2>

                <div class="flex items-center gap-4">

                    <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Terminal Chimalhuacán
                    </div>

                    <!-- MENÚ DE USUARIO - ESQUINA SUPERIOR DERECHA -->
                    <div class="relative">
                        <button id="userMenuBtn" type="button"
                                class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full hover:bg-slate-100 transition border border-transparent hover:border-slate-200">

                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs text-white shrink-0">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                            </div>

                            <p class="text-xs font-semibold text-slate-800 hidden sm:block">{{ Auth::user()->name ?? 'Usuario' }}</p>

                            <i id="userMenuArrow" class="ri-arrow-down-s-line text-slate-400 shrink-0 transition-transform"></i>
                        </button>

                        <!-- MENÚ DESPLEGABLE -->
                        <div id="userMenuDropdown"
                             class="hidden absolute top-full right-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden z-50">

                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                                <p class="text-[10px] text-blue-600 uppercase font-bold tracking-wider">{{ Auth::user()->role ?? 'operador' }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                                <i class="ri-user-line"></i>
                                Mi Perfil
                            </a>

                            <div class="border-t border-slate-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition">
                                    <i class="ri-logout-box-r-line"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </header>

            <!-- ÁREA DE CONTENIDO -->
            <main class="p-8 flex-1">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Script del menú de usuario (abrir/cerrar dropdown) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('userMenuBtn');
            const dropdown = document.getElementById('userMenuDropdown');
            const arrow = document.getElementById('userMenuArrow');

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                    arrow.classList.remove('rotate-180');
                }
            });
        });
    </script>
</body>
</html>