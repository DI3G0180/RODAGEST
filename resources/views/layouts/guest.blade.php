<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F8FAFC">
    <title>RODAGEST — @yield('title', 'Acceso seguro')</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8FAFC] font-sans text-[#083344] antialiased">
    @include('components.splash-screen')
    <main class="grid min-h-screen place-items-center px-4 py-8">
        <div class="w-full max-w-lg rounded-3xl border border-slate-200 bg-white p-5 shadow-xl sm:p-8">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
