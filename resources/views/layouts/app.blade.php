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
    <script>tailwind.config={theme:{extend:{colors:{ink:'#05161A',deep:'#072E33',primary:'#0C7075',cyan:'#0F969C',secondary:'#6DA5C0',tertiary:'#6DA5C0',steel:'#294D61',light:'#F4F9FB',cream:'#F4F9FB',action:'#0F969C'}}}}</script>
    <style>h1,h2{color:#05161A}thead{background:#072E33!important;color:#fff!important}thead th{color:#fff!important}</style>
</head>
<body class="min-h-screen bg-[#F4F9FB] font-sans text-[#05161A] antialiased">
    @include('partials.role-menu')
    <main class="min-w-0">@yield('content')</main>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
