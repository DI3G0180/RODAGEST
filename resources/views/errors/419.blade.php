<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <title>RODAGEST — Sesión vencida</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="grid min-h-screen place-items-center bg-[#F4F9FB] px-4 py-8 text-[#05161A]">
    <main class="w-full max-w-lg rounded-2xl border border-[#6DA5C0]/50 bg-white p-6 text-center shadow-lg sm:p-9">
        <img src="/images/logo-rodagest.png" alt="RODAGEST" class="mx-auto mb-5 h-16 w-auto max-w-48 object-contain [mix-blend-mode:multiply]">
        <p class="text-sm font-bold uppercase tracking-widest text-[#0C7075]">Error 419</p>
        <h1 class="mt-2 text-2xl font-extrabold">La sesión venció</h1>
        <p class="mt-3 text-sm leading-6 text-[#294D61]">La página de acceso quedó abierta con un token anterior o el navegador cambió de dirección. Vuelve al inicio de sesión para generar una sesión nueva.</p>
        <a href="{{ route('login', [], false) }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg bg-[#0F969C] px-5 py-3 font-bold text-white hover:bg-[#072E33]">Volver a iniciar sesión</a>
        <p class="mt-5 text-xs leading-5 text-[#294D61]">Mantén la misma dirección durante el acceso: usa `127.0.0.1:8000` o `localhost:8000`, sin alternar entre ambas.</p>
    </main>
</body>
</html>
