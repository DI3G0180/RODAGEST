<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <svg class="h-9 w-auto fill-current text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3v1h4v10h-5v7l3 3V7l-3 3H6v5h2v7H5v5h2v-7H5v-5h2v7H3v-5H2v5H1c-1.1 0-2 .9-2 2l1 7h12l1-7c0-1.1-.9-2-2-2zm0-2c-2.2 0-4 1.8-4 4v4h8v-4c0-2.2-1.8-4-4-4zm0 5.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                        </svg>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
                    <a href="{{ route('inspecciones.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-primary hover:bg-secondary transition-all duration-150 @if(request()->routeIs('inspecciones.index')) class='bg-secondary text-white'@endif>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h3.066l2.55-5.42a2 2 0 011.86-.56l3.33 5.07a2 2 0 01-.63 1.97H21"/>
                        </svg>
                        <span class="hidden md-block">Inspecciones</span>
                    </a>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente' || auth()->user()->role === 'limpieza')
                    <a href="{{ route('aseos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-primary hover:bg-secondary transition-all duration-150 @if(request()->routeIs('aseos.index')) class='bg-secondary text-white'@endif>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13V6a2 2 0 10-4 0v2.253m0 0l-5.586 5.586-.071-.072A5.5 5.5 0 1012 3.593l5.586 5.586-.071-.072m13.707-4.072a3 3 0 11-4.243 4.243L18 21.753V5.253z"/>
                        </svg>
                        <span class="hidden md-block">Aseo</span>
                    </a>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('unidades.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-primary hover:bg-secondary transition-all duration-150 @if(request()->routeIs('unidades.index')) class='bg-secondary text-white'@endif>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="hidden md-block">Unidades</span>
                    </a>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-500 hover:text-primary hover:bg-secondary transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6a2 2 0 00-2-2H5a2 2 0 00-2 2v13m2-3a3 3 0 11-6 0 3 3 0 016 0l3 3m3-3a3 3 0 10-6 0 3 3 0 016 0l3 3m3-3a3 3 0 10-6 0 3 3 0 016 0l3 3"/>
                        </svg>
                        <span class="hidden md-block">Quejas</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-primary hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 fill-current" stroke="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100">
                Dashboard
            </a>

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'inspector')
            <a href="{{ route('inspecciones.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100">
                Inspecciones
            </a>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'intendente' || auth()->user()->role === 'limpieza')
            <a href="{{ route('aseos.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100">
                Aseo
            </a>
            @endif

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('unidades.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100">
                Unidades
            </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100">
                    Perfil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="block px-3 py-2 rounded-md text-sm font-medium text-gray-800 hover:bg-gray-100" onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>