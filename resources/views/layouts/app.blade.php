<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'RODAGEST · Gestión de flotas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#018FC7',secondary:'#83C0DF',tertiary:'#B1D0E2',light:'#DBE7F3',cream:'#FBF1E5'}}}}</script>
</head>
<body class="min-h-screen bg-[#FBF1E5] font-sans text-slate-800 antialiased">
    <header class="border-b border-[#B1D0E2] bg-[#FBF1E5] shadow-sm">
        <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6">
            <a href="{{ route('dashboard', [], false) }}" class="flex min-w-0 items-center gap-3"><img src="/images/logo-rodagest.png" alt="RODAGEST" class="h-10 w-auto max-w-32 object-contain [mix-blend-mode:multiply]"><span class="hidden font-bold text-[#018FC7] sm:inline">Gestión de flotas</span></a>
            @auth
                <div class="flex items-center gap-3"><span class="hidden text-right sm:block"><b class="block text-sm">{{ auth()->user()->name }}</b><small class="uppercase text-[#018FC7]">{{ auth()->user()->role }}</small></span><form method="POST" action="{{ route('logout', [], false) }}">@csrf<button class="rounded-lg border border-[#B1D0E2] px-3 py-2 text-sm font-semibold text-[#018FC7] hover:bg-[#DBE7F3]">Salir</button></form></div>
            @endauth
        </div>
        @include('partials.role-menu')
    </header>
    <main class="min-w-0">@yield('content')</main>
</body>
</html>
