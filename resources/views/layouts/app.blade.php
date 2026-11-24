<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RODAGEST — @yield('title', 'Gestión de flotas')</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>tailwind.config={theme:{extend:{colors:{ink:'#0A0F14',deep:'#0F3D3E',primary:'#2E6F73',secondary:'#7FB3B8',tertiary:'#7FB3B8',light:'#EADCC8',cream:'#EADCC8',action:'#C6601F'}}}}</script>
</head>
<body class="min-h-screen bg-[#EADCC8] font-sans text-[#0A0F14] antialiased">
    @include('partials.role-menu')
    <main class="min-w-0">@yield('content')</main>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
