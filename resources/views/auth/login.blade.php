<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RODAGEST · Iniciar Sesión</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #DBE7F3 0%, #B1D0E2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            padding: 16px;
        }
        /* Splash Screen animado que se oculta solo */
        #splash {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #FBF1E5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            animation: hideSplash 0.45s ease-out 1.2s forwards;
        }
        @keyframes hideSplash {
            to { opacity: 0; visibility: hidden; pointer-events: none; }
        }
        .progress-track {
            width: 210px;
            height: 6px;
            background: #DBE7F3;
            border-radius: 999px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #83C0DF, #018FC7);
            border-radius: 999px;
            animation: fillBar 1.15s ease-out forwards;
        }
        @keyframes fillBar { to { width: 100%; } }

        /* Tarjeta de Login */
        .card {
            width: 100%;
            max-width: 410px;
            background: #FBF1E5;
            border: 1.5px solid #B1D0E2;
            border-top: 5px solid #018FC7;
            border-radius: 20px;
            padding: 36px 28px;
            box-shadow: 0 20px 45px rgba(1, 143, 199, 0.16);
        }
        .logo {
            width: 185px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 10px;
            object-fit: contain;
        }
        .subtitle {
            text-align: center;
            color: #018FC7;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .badge {
            display: block;
            width: fit-content;
            margin: 0 auto 24px;
            background: #DBE7F3;
            color: #018FC7;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 999px;
            border: 1px solid #B1D0E2;
        }
        .alert {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .group { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .input-wrap { position: relative; width: 100%; }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            height: 46px;
            padding: 0 42px 0 14px;
            background: #DBE7F3;
            border: 1.5px solid #B1D0E2;
            border-radius: 12px;
            font-size: 15px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
        }
        input:focus {
            background: #ffffff;
            border-color: #018FC7;
            box-shadow: 0 0 0 3px rgba(1, 143, 199, 0.18);
        }
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #018FC7;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #334155;
            margin-bottom: 22px;
            cursor: pointer;
        }
        .btn-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #018FC7 0%, #0277a8 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(1, 143, 199, 0.28);
            transition: transform 0.15s, opacity 0.15s;
        }
        .btn-submit:hover { opacity: 0.94; transform: translateY(-1px); }
        .footer-note {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #B1D0E2;
            text-align: center;
            font-size: 11.5px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div id="splash">
        <img src="{{ asset('images/logo-rodagest.png') }}" alt="RODAGEST" style="width: 195px; height: auto;">
        <div class="progress-track"><div class="progress-fill"></div></div>
    </div>

    <div class="card">
        <img src="{{ asset('images/logo-rodagest.png') }}" alt="RODAGEST" class="logo">
        <div class="subtitle">Control Operativo</div>
        <span class="badge">Acceso Corporativo Autorizado</span>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ str_contains($error, 'auth.failed') ? 'Credenciales incorrectas. Verifica tu correo y contraseña.' : $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="group">
                <label for="email">Correo Electrónico</label>
                <div class="input-wrap">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="usuario@rodagest.com">
                </div>
            </div>

            <div class="group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="eye-btn" onclick="const p=document.getElementById('password'); p.type=p.type==='password'?'text':'password';">Ver</button>
                </div>
            </div>

            <label class="remember">
                <input type="checkbox" name="remember"> Recordar sesión
            </label>

            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>

        <div class="footer-note"> Sistema de Gestión de Flotas RODAGEST</div>
    </div>

    <script>
        // Ocultar splashscreen después de cargar recursos (1.2s para animación CSS + margen)
        setTimeout(() => { const s = document.getElementById('splash'); if (s) s.style.display = 'none'; }, 1500);
    </script>
</body>
</html>