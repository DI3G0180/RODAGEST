<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RODAGEST — @yield('title', 'Acceso')</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=3">
    <link rel="shortcut icon" href="/favicon.ico?v=3">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=3">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 antialiased">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-white rounded-xl border border-slate-200 w-full max-w-md p-8 shadow-xl animate-fade-in-up">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-lg primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 16l2-2m8 4l2-2m8 4l2-2M6 20 horizontal-8l2-2M4 6l2-2M4 6l2-2m16 0a4 4 0 11-8 0 4 4 0 018 0l-8 8Z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold primary">RODAGEST</h1>
                <p class="text-slate-600">Sistema de Gestión de Unidades</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Correo Electrónico</label>
                    <input type="email" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input type="password" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:outline-none bg-cream">
                </div>

                <div>
                    <button type="submit" class="w-full px-4 py-3 rounded-lg text-sm font-medium primary text-white hover:bg-secondary transition-all duration-300">
                        <i class="ri-login-circle-line text-sm mr-2 align-middle"></i>
                        Ingresar al Sistema
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
