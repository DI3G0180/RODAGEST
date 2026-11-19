<!DOCTYPE html>
<html lang="es" x-data="{ open: false }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RODAGEST - Panel Control</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
       Barra Superior (Navbar fija)
       ============================================ -->
    <nav class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-[#B1D0E2] shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6">

            <!-- Botón hamburguesa en móvil -->
            <button @click="open = !open" class="md:hidden p-2 text-slate-600 hover:text-primary transition" aria-label="Abrir menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Logo y título -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-rodagest.png') }}" alt="RodaGest Logo" class="h-10 w-auto mix-blend-multiply">
                <div>
                    <h1 class="text-xl font-bold primary tracking-tight">RODAGEST</h1>
                    <p class="text-slate-600 text-sm">Control Operativo</p>
                </div>
            </div>

            <!-- Usuario y Cerrar Sesión -->
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-600">{{ Auth::user() ? Auth::user()->name : 'Invitado' }}</span>
                <span class="bg-[#DBE7F3] text-[#018FC7] text-xs px-2 py-1 rounded">{{ Auth::user() ? Auth::user()->role : 'Invitado' }}</span>

                <form method="POST" action="{{ route('logout') }}" class="hidden" id="logout-form">
                    @csrf
                </form>
                <button class="text-sm text-red-600 hover:text-red-800" onclick="document.getElementById('logout-form').submit;">
                    Cerrar Sesión
                </button>
            </div>

            <!-- Selector de Vista de Rol (solo para admin) -->
            @if(auth()->user() && auth()->user()->role === 'admin')
            <div class="hidden md:block flex items-center gap-2">
                <button @click="vistaActual = 'admin'" @mouseenter="setTimeout(()=>{btnAdmin.classList.add('bg-[#165a7c]')}","mouseleave="btnAdmin.classList.remove('bg-[#165a7c]")" class="relative px-4 py-2 text-sm font-medium text-white hover:bg-[#165a7c] transition-all duration-150 {{ vistaActual === 'admin' ? 'bg-[#165a7c]' : 'bg-transparent' }}" title="Vista Administrador">
                    <span>Admin</span>
                </button>
                <button @click="vistaActual = 'inspector'" @mouseenter="setTimeout(()=>{btnInspec.classList.add('bg-[#165a7c]')}","mouseleave="btnInspec.classList.remove('bg-[#165a7c]")" class="relative px-4 py-2 text-sm font-medium text-white hover:bg-[#165a7c] transition-all duration-150 {{ vistaActual === 'inspector' ? 'bg-[#165a7c]' : 'bg-transparent' }}" title="Vista Inspector">
                    <span>Inspector</span>
                </button>
                <button @click="vistaActual = 'operador'" @mouseenter="setTimeout(()=>{btnOperador.classList.add('bg-[#165a7c]')}","mouseleave="btnOperador.classList.remove('bg-[#165a7c]")" class="relative px-4 py-2 text-sm font-medium text-white hover:bg-[#165a7c] transition-all duration-150 {{ vistaActual === 'operador' ? 'bg-[#165a7c]' : 'bg-transparent' }}" title="Vista Operador">
                    <span>Operador</span>
                </button>
                <button @click="vistaActual = 'limpieza'" @mouseenter="setTimeout(()=>{btnLimpieza.classList.add('bg-[#165a7c]')}","mouseleave="btnLimpieza.classList.remove('bg-[#165a7c]")" class="relative px-4 py-2 text-sm font-medium text-white hover:bg-[#165a7c] transition-all duration-150 {{ vistaActual === 'limpieza' ? 'bg-[#165a7c]' : 'bg-transparent' }}" title="Vista Limpieza">
                    <span>Limpieza</span>
                </button>
            </div>
            @endif

        </div>
    </nav>

    <!-- ============================================
       Menú Lateral (Sidebar)
       ============================================ -->
    <aside class="hidden md:flex md:w-64 md:flex-col md:height-screen bg-gradient-to-b from-[#018FC7] to-[#016791] text-white border-r border-[#B1D0E2]" x-data="{ open: false }" :class="{'translate-x-0': open}" style="transform: translateX(0);">
        <div class="flex items-center justify-between mb-6">
            <img src="{{ asset('images/logo-rodagest.png') }}" alt="RodaGest Logo" class="h-8 w-auto">
            <button @click="open = false" class="text-white/60 hover:text-white transition absolute right-0 top-0 p-2" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex flex-col gap-1 p-2">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#165a7c] transition-all duration-150 @if(request()->routeIs('dashboard')) class='bg-[#165a7c] text-white'@endif">
                        <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                        </svg>
                        <span class="hidden md-block">Dashboard</span>
                    </a>
                </li>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
                <li>
                    <a href="{{ route('inspecciones.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] transition-all duration-150 @if(request()->routeIs('inspecciones.index')) class='bg-[#1a73e8] text-white'@endif">
                        <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                        <span class="hidden md-block">Inspecciones</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente')
                <li>
                    <a href="{{ route('aseos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] transition-all duration-150 @if(request()->routeIs('aseos.index')) class='bg-[#1a73e8] text-white'@endif">
                        <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13V6a2 2 0 10-4 0v2.253m0 0l-5.586 5.586-.071-.072A5.5 5.5 0 1012 3.593l5.586 5.586-.071-.072m13.707-4.072a3 3 0 11-4.243 4.243L18 21.753V5.253z"/>
                        </svg>
                        <span class="hidden md-block">Aseo</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('unidades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] transition-all duration-150 @if(request()->routeIs('unidades.index')) class='bg-[#1a73e8] text-white'@endif">
                        <svg class="w-5 h-5 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="hidden md-block">Unidades</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </aside>

    <!-- ============================================
       Contenido Principal
       ============================================ -->
    <div class="flex-1 overflow-x-auto p-4 sm:p-6 lg:p-8">

        <!-- ============================================
           Selector Rápido de Rol (Mobile + Admin Desktop)
           ============================================ -->
        @if(auth()->check() && auth()->user()->role === 'admin')
        <div class="bg-white rounded-2xl p-6 mb-8 shadow-sm border border-[#B1D0E2] mb-8">
            <h2 class="font-semibold text-slate-800 mb-4">Cambiar de Vista de Rol</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="#" x-data="() => ({vista: 'admin'})" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3 hover:x-toggle" x-toggle=".admin-panel">
                    <svg class="w-5 h-5 text-[#018FC7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium">Vista Administrador</p>
                        <p class="text-xs text-slate-500">Panel ejecutivo completo</p>
                    </div>
                </a>

                <a href="#" x-data="() => ({vista: 'inspector'})" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3 hover:x-toggle" x-toggle=".inspector-panel">
                    <svg class="w-5 h-5 text-[#83C0DF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium">Vista Inspector</p>
                        <p class="text-xs text-slate-500">Panel de inspecciones técnicas</p>
                    </div>
                </a>

                <a href="#" x-data="() => ({vista: 'operador'})" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3 hover:x-toggle" x-toggle=".operador-panel">
                    <svg class="w-5 h-5 text-[#018FC7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium">Vista Operador</p>
                        <p class="text-xs text-slate-500">Panel de flota y rutas</p>
                    </div>
                </a>

                <a href="#" x-data="() => ({vista: 'limpieza'})" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3 hover:x-toggle" x-toggle=".limpieza-panel">
                    <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium">Vista Limpieza</p>
                        <p class="text-xs text-slate-500">Panel de higiene y lavado</p>
                    </div>
                </a>
            </div>
        </div>
        @endif

        <!-- ============================================
           Contenido Dinámico por Rol
           ============================================ -->
        <div class="max-w-7xl mx-auto">

            @if(auth()->check())
                @switch(vistaActual ?? auth()->user()->role)
                    @case('admin')
                        @include('dashboard.admin')
                    @case('inspector')
                        @include('dashboard.inspector')
                    @case('operador')
                        @include('dashboard.operador')
                    @case('limpieza')
                        @include('dashboard.limpieza')
                    @default
                        <p class="text-slate-600">Bienvenido al panel</p>
                @endswitch
            @else
                <!-- Cuadrícula de KPIs cuando hay usuario logueado -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <!-- KPI: Total Unidades -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-[#B1D0E2]" style="transition: all 0.3s ease;">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-[#FBF1E5] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#018FC7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-slate-500">Unidades en Flota</p>
                                <p class="text-2xl font-bold text-[#018FC7]" id="kpi-unidades">Cargando...</p>
                            </div>
                        </div>
                    </div>

                    <!-- KPI: Inspecciones Pendientes -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-[#B1D0E2]" style="transition: all 0.3s ease;">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-[#DBE7F3] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#83C0DF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-slate-500">Inspecciones Pendientes</p>
                                <p class="text-2xl font-bold text-[#83C0DF]" id="kpi-inspecciones">Cargando...</p>
                            </div>
                        </div>
                    </div>

                    <!-- KPI: Incidencias -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-[#B1D0E2]" style="transition: all 0.3s ease;">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-[#FBF1E5] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 9l-3 3m-3-3l3-3m12-3v2m0 9l3-3m-3 3l3 3"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-slate-500">Incidencias Abiertas</p>
                                <p class="text-2xl font-bold text-[#EF4444]" id="kpi-incidencias">Cargando...</p>
                            </div>
                        </div>
                    </div>

                    <!-- KPI: Usuarios Activos -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-[#B1D0E2]" style="transition: all 0.3s ease;">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-slate-500">Usuarios Activos</p>
                                <p class="text-2xl font-bold text-primary" id="kpi-usuarios">Cargando...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acciones Rápidas -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-[#B1D0E2] mb-8">
                    <h2 class="font-semibold text-slate-800 mb-4">Acciones Rápidas</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Botón que siempre debe aparecer para todos los roles -->
                        <a href="{{ route('inspecciones.index') }}" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3">
                            <svg class="w-5 h-5 text-[#018FC7] group-hover:text-[#165a7c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium">Inspecciones</p>
                                <p class="text-xs text-slate-500">Ver y gestionar inspecciones</p>
                            </div>
                        </a>

                        <a href="#" class="group bg-[#FBF1E5] border border-[#B1D0E2] rounded-xl p-4 hover:bg-[#DBE7F3] transition-colors flex items-center gap-3">
                            <svg class="w-5 h-5 text-[#018FC7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 8l4 4m0 0l4-4m-4 4l-4-4m4 4l4-4"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium">Reportar Incidencia</p>
                                <p class="text-xs text-slate-500">Registrar falla o incidencia</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================
       Sidebar Colapsable (modo móvil)
       ============================================ -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900/80 backdrop-blur-xl transform translate-x-full transition-transform duration-300 ease-in-out" :class="{'translate-x-0': open}" x-show="open" x-cloak aria-label="Menú lateral móvil">
        <div class="flex items-center justify-between p-4 mb-6">
            <h2 class="text-xl font-bold text-white">Menú</h2>
            <button @click="open = false" class="text-white/60 hover:text-primary transition" aria-label="Cerrar menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="flex flex-col gap-1 px-2">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7l-7-7m7 7v12a4 4 0 01-4 4H3a4 4 0 01-4-4V3"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
                <li>
                    <a href="{{ route('inspecciones.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                        <span>Inspecciones</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente')
                <li>
                    <a href="{{ route('aseos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] hover:text-primary transition-all duration-150">
                        <svg class="w-4 h-4 fill-current" stroke="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13V6a2 2 0 10-4 0v2.253m0 0l-5.586 5.586-.071-.072A5.5 5.5 0 1012 3.593l5.586 5.586-.071-.072m13.707-4.072a3 3 0 11-4.243 4.243L18 21.753V5.253z"/>
                        </svg>
                        <span>Aseo</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('unidades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-[#1a73e8] hover:text-primary transition-all duration-150">
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

    <script>
        // Inicializar menú móvil
        open = false;
        vistaActual = '@if(auth()->check()){{ auth()->user()->role }}@endif';

        // Cerrar menú móvil al hacer click fuera
        document.addEventListener('click', (e) => {
            const sidebar = document.querySelector('aside');
            const hamburger = document.querySelector('button[aria-label="Abrir menú"]');
            if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
                sidebar.style.transform = 'translateX(0)';
                // Re-aplicar clase para el efecto
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('translate-x-full');
            }
        });

        // Lógica del selector de vista de rol para Admin
        @if(auth()->user() && auth()->user()->role === 'admin')
        document.addEventListener('DOMContentLoaded', () => {
            const btnAdmin = document.querySelector('button[title="Vista Administrador"]');
            const btnInspec = document.querySelector('button[title="Vista Inspector"]');
            const btnOperador = document.querySelector('button[title="Vista Operador"]');
            const btnLimpieza = document.querySelector('button[title="Vista Limpieza"]');
            const panels = document.querySelectorAll('.admin-panel, .inspector-panel, .operador-panel, .limpieza-panel');
            
            // Estado inicial
            let vistaActual = 'admin';
            
            // Mostrar panel correcto y resaltar botón
            function mostrarVista(vista) {
                vistaActual = vista;
                panels.forEach(p => p.classList.add('hidden'));
                const btns = [btnAdmin, btnInspec, btnOperador, btnLimpieza];
                btns.forEach(b => b.classList.remove('bg-[#165a7c]'));
                
                const panelMap = {
                    'admin': '.admin-panel',
                    'inspector': '.inspector-panel',
                    'operador': '.operador-panel',
                    'limpieza': '.limpieza-panel'
                };
                
                if(panelMap[vista]) {
                    document.querySelector(panelMap[vista]).classList.remove('hidden');
                    if(btns[Object.keys(panelMap).indexOf(vista)]) {
                        btns[Object.keys(panelMap).indexOf(vista)].classList.add('bg-[#165a7c]');
                    }
                }
            }
            
            // Hover effects con delay
            [btnAdmin, btnInspec, btnOperador, btnLimpieza].forEach((btn, i) => {
                btn.addEventListener('mouseenter', () => {
                    setTimeout(() => {
                        if(vistaActual !== btn.dataset.vista) {
                            btn.classList.add('bg-[#165a7c]');
                        }
                    }, 150);
                });
                btn.addEventListener('mouseleave', () => {
                    if(vistaActual !== btn.dataset.vista) {
                        btn.classList.remove('bg-[#165a7c]');
                    }
                });
            });
            
            // Click en botones
            btnAdmin.addEventListener('click', () => mostrarVista('admin'));
            btnInspec.addEventListener('click', () => mostrarVista('inspector'));
            btnOperador.addEventListener('click', () => mostrarVista('operador'));
            btnLimpieza.addEventListener('click', () => mostrarVista('limpieza'));
            
            // Initialize
            mostrarVista('admin');
        });
        @endif
    </script>
</body>
</html>