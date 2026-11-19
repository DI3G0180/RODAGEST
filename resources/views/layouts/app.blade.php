<!DOCTYPE html>
<html lang="es" x-data="{ open: false }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RODAGEST - Sistema de Gestión</title>

    <!-- Fontes -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                        primary: '#018FC7',
                        secondary: '#83C0DF',
                        tertiary: '#B1D0E2',
                        light: '#DBE7F3',
                        cream: '#FBF1E5',
                    },
                    colors: {
                        primary: '#018FC7',
                        secondary: '#83C0DF',
                        tertiary: '#B1D0E2',
                        light: '#DBE7F3',
                        cream: '#FBF1E5',
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full bg-cream font-sans">

    <!-- ============================================
       Splash Screen Superior (Sólo en login)
       ============================================ -->
    @if(Request::is('login') || Request::is(''))
    <div id="splashScreen" class="splash-screen fixed inset-0 z-50 flex-col items-center justify-between pointer-events-none">
        <div class="flex flex-col items-center pointer-events-auto">
            <img src="{{ asset('images/logo-rodagest.png') }}" alt="RodaGest Logo" class="logo-splash">
            <p class="splash-text mt-2">Iniciando entorno seguro...</p>
            <div class="progress-bar mt-2">
                <div class="progress-fill" style="width: 100%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================
       Barra Superior (Navbar)
       ============================================ -->
    <nav class="bg-white border-b border-tertiary shadow-sm sticky top-0 z-50 backdrop-blur-md">

        <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-6">

            <!-- Logo a la izquierda -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-rodagest.png') }}" alt="RodaGest Logo" class="logo-nav h-10 w-auto">
                </div>
                <div>
                    <h1 class="text-xl font-bold primary tracking-tight">RODAGEST</h1>
                    <p class="text-slate-600 text-sm">Gestión de Unidades y Reportes</p>
                </div>
            </div>

            <!-- Área de usuario a la derecha -->
            <div class="flex items-center gap-4">

                <!-- Bot menú hamburguesa para móvil (siempre visible en md:hidden) -->
                <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Menú principal">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Dropdown de sesión (solo en md:block) -->
                <div class="hidden md:block items-center gap-4">
                    <!-- Nombre y rol del usuario -->
                    <div class="hidden md:flex items-center gap-2 text-sm text-slate-700">
                        <span>{{ Auth::user()->name }}</span>
                        <span class="bg-secondary text-white text-xs px-2 py-1 rounded"> {{ Auth::user()->role }} </span>
                    </div>
                    
                    <!-- Botón Cerrar Sesión -->
                    <div class="relative hidden md:block">
                        <a href="{{ route('logout') }}" class="block py-2 px-4 text-sm text-red-600 hover:bg-red-50 rounded transition" onclick="event.preventDefault(); document.getElementById('logout-form').submit;">
                            <i class="ri-logout-circle-line text-xs mr-2 align-middle"></i>
                            Cerrar Sesión
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================
       Contenido Principal
       ============================================ -->
    <div class="flex min-h-screen">

        <!-- ============================================
           Menú Lateral (Sidebar)
           ============================================ -->
        <aside class="w-64 min-w-[260px] bg-gradient-to-b from-primary to-primary-800 text-white border-r border-secondary shrink-0 transition-all duration-300 ease-in-out" :class="{'w-16': open === false, 'flex': open}" x-data x-init="() => ({ open: true })">
            <div class="flex flex-col h-full p-4 pt-6">

                <!-- Logo en sidebar -->
                <div class="flex items-center justify-between mb-6">
                    <img src="{{ asset('images/logo-rodagest.png') }}" alt="RodaGest Logo" class="sidebar-logo h-12 w-auto">
                    <button @click="open = false" class="text-white/60 hover:text-white transition" aria-label="Cerrar menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="flex flex-col gap-1">
                    <ul class="space-y-1">

                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150 @if(request()->routeIs('dashboard')) class='bg-secondary/20 text-white'@endif">
                                <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                                </svg>
                                <span class="hidden md-block">Dashboard</span>
                            </a>
                        </li>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
                        <li>
                            <a href="{{ route('inspecciones.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150 @if(request()->routeIs('inspecciones.index')) class='bg-secondary/20 text-white'@endif">
                                <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                                </svg>
                                <span class="hidden md-block">Inspecciones</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente')
                        <li>
                            <a href="{{ route('aseos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150 @if(request()->routeIs('aseos.index')) class='bg-secondary/20 text-white'@endif>
                                <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13V6a2 2 0 10-4 0v2.253m0 0l-5.586 5.586-.071-.072A5.5 5.5 0 1012 3.593l5.586 5.586-.071-.072m13.707-4.072a3 3 0 11-4.243 4.243L18 21.753V5.253z"/>
                                </svg>
                                <span class="hidden md-block">Aseo</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('unidades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150 @if(request()->routeIs('unidades.index')) class='bg-secondary/20 text-white'@endif">
                                <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="hidden md-block">Unidades</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- ============================================
           Contenido Principal
           ============================================ -->
        <div class="flex-1 overflow-x-auto">

            @yield('content')

        </div>
    </div>

    <!-- ============================================
       Sidebar Colapsable (modo móvil)
       ============================================ -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900/80 backdrop-blur-xl transform translate-x-full transition-transform duration-300 ease-in-out" :class="{'translate-x-0': open}" x-show="open" x-cloak aria-label="Menú lateral móvil">
        <div class="flex items-center justify-between p-4 mb-6">
            <h2 class="text-xl font-bold text-white">Menú</h2>
            <button @click="open = false" class="text-white/60 hover:text-primary transition" aria-label="Cerrar menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="flex flex-col gap-1 px-2">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
                <li>
                    <a href="{{ route('inspecciones.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                        <span>Inspecciones</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente')
                <li>
                    <a href="{{ route('aseos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13V6a2 2 0 10-4 0v2.253m0 0l-5.586 5.586-.071-.072A5.5 5.5 0 1012 3.593l5.586 5.586-.071-.072m13.707-4.072a3 3 0 11-4.243 4.243L18 21.753V5.253z"/>
                        </svg>
                        <span>Aseo</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('unidades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-secondary/20 hover:text-primary transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Unidades</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </aside>
</body>
</html>