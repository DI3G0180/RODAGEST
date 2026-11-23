<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RODAGEST — @yield('title', 'Gestión de flotas')</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=3">
    <link rel="shortcut icon" href="/favicon.ico?v=3">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=3">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#018FC7',secondary:'#83C0DF',tertiary:'#B1D0E2',light:'#DBE7F3',cream:'#FBF1E5'}}}}</script>
</head>
<body class="min-h-screen bg-[#FBF1E5] font-sans text-slate-800 antialiased">
    @include('partials.role-menu')
    <main class="min-w-0">@yield('content')</main>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
