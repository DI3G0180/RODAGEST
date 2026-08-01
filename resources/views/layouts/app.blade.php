<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RodaGest') }} - @yield('title', 'Control')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Remixicon (Carga estable con cdnjs) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" integrity="sha512-6KOtlyfc+DdW7dEJA3OHy785h7e+X38h8WJ+sL0Bv+99n9+W9Zp5N45f7j4f+n6+N6W5n9+n4=" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

            <!-- FOOTER DEL SIDEBAR / USUARIO Y CERRAR SESIÓN -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/80">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs text-white shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white truncate" style="color: #ffffff !important;">{{ Auth::user()->name ?? 'Usuario' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'admin@rodagest.com' }}</p>
                        </div>
                    </div>
                    
                    <!-- Botón para cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Cerrar Sesión" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition">
                            <i class="ri-logout-box-r-line text-lg"></i>
                        </button>
                    </form>
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
                
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Terminal Chimalhuacán
                </div>
            </header>

            <!-- ÁREA DE CONTENIDO -->
            <main class="p-8 flex-1">
                @yield('content')
            </main>

        </div>
    </div>
</body>
</html>