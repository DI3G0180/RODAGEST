<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="shortcut icon" href="/favicon.ico?v=2">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">
    <title>RODAGEST — Sesión vencida</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="grid min-h-screen place-items-center bg-[#FBF1E5] px-4 py-8 text-slate-800">
    <main class="w-full max-w-lg rounded-2xl border border-[#B1D0E2] bg-white p-6 text-center shadow-lg sm:p-9">
        <img src="/images/logo-rodagest.png" alt="RODAGEST" class="mx-auto mb-5 h-16 w-auto max-w-48 object-contain [mix-blend-mode:multiply]">
        <p class="text-sm font-bold uppercase tracking-widest text-[#018FC7]">Error 419</p>
        <h1 class="mt-2 text-2xl font-extrabold">La sesión venció</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">La página de acceso quedó abierta con un token anterior o el navegador cambió de dirección. Vuelve al inicio de sesión para generar una sesión nueva.</p>
        <a href="{{ route('login', [], false) }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg bg-[#018FC7] px-5 py-3 font-bold text-white hover:bg-sky-700">Volver a iniciar sesión</a>
        <p class="mt-5 text-xs leading-5 text-slate-500">Mantén la misma dirección durante el acceso: usa `127.0.0.1:8000` o `localhost:8000`, sin alternar entre ambas.</p>
    </main>
</body>
</html>
