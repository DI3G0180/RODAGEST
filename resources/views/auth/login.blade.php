<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RodaGest — Acceso al Sistema</title>
    
    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Tailwind Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Estilos base garantizados por si falla la compilación de Tailwind */
        body {
            background-color: #0f172a !important; /* Fondo slate-900 */
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background-color: #1e293b; /* Slate-800 */
            border: 1px solid #334155;
            border-radius: 1rem;
            width: 100%;
            max-width: 420px; /* Ampliado para dar más espacio visual al logo */
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            box-sizing: border-box;
        }

        .input-group {
            margin-bottom: 1.25rem;
        }

        .input-label {
            display: block;
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            color: #64748b;
            font-size: 1.1rem;
            pointer-events: none;
        }

        .custom-input {
            width: 100%;
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            color: #ffffff;
            font-size: 0.875rem;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .custom-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        /* Quitar el fondo azul del autorrelleno de Chrome */
        .custom-input:-webkit-autofill,
        .custom-input:-webkit-autofill:hover, 
        .custom-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff;
            -webkit-box-shadow: 0 0 0px 1000px #0f172a inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        .btn-submit {
            width: 100%;
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.85rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        /* Estilo optimizado para destacar el Logo */
        .brand-logo-img {
            max-width: 270px; /* Aumentado de 180px a 270px */
            width: 100%;
            height: auto;
            margin: 0 auto 0.5rem auto;
            display: block;
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <!-- LOGO Y TÍTULO -->
        <div style="text-align: center; margin-bottom: 1.75rem;">
            <img src="{{ asset('images/LOGO RodaGestt.jpg') }}" alt="RodaGest Logo" class="brand-logo-img">
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 500;">Control Operativo</p>
        </div>

        <!-- ERRORES DE VALIDACIÓN -->
        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; font-size: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <p style="margin: 0;">⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORMULARIO -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- CORREO ELECTRÓNICO -->
            <div class="input-group">
                <label class="input-label" for="email">Correo Electrónico</label>
                <div class="input-wrapper">
                    <i class="ri-mail-line"></i>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="admin@rodagest.com" 
                           class="custom-input">
                </div>
            </div>

            <!-- CONTRASEÑA -->
            <div class="input-group">
                <label class="input-label" for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="ri-lock-line"></i>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           class="custom-input">
                </div>
            </div>

            <!-- RECORDAR SESIÓN -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem;">
                <label style="display: flex; align-items: center; color: #94a3b8; font-size: 0.75rem; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember" style="margin-right: 0.5rem; accent-color: #2563eb;">
                    Recordar sesión
                </label>
            </div>

            <!-- BOTÓN INGRESAR -->
            <button type="submit" class="btn-submit">
                <span>Ingresar al Sistema</span>
                <i class="ri-arrow-right-line"></i>
            </button>
        </form>

    </div>

</body>
</html>