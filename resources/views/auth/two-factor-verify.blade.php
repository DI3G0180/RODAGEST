<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FBF1E5">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="shortcut icon" href="/favicon.ico?v=2">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">
    <title>RODAGEST — Verificación en dos pasos</title>
    <style>
        *{box-sizing:border-box}body{min-height:100vh;margin:0;display:grid;place-items:center;padding:16px;background:#FBF1E5;color:#183647;font-family:system-ui,sans-serif}.card{width:min(100%,420px);padding:clamp(24px,6vw,40px);border:1px solid #B1D0E2;border-radius:22px;background:white;box-shadow:0 18px 55px #17445b16}img{display:block;width:min(180px,60%);height:80px;object-fit:contain;mix-blend-mode:multiply;margin:0 auto 18px}h1{text-align:center;margin:0;font-size:26px}.hint{text-align:center;color:#607985;font-size:14px;line-height:1.5}.field{width:100%;min-height:54px;border:1px solid #B1D0E2;border-radius:10px;text-align:center;font-size:24px;letter-spacing:.35em}.btn{width:100%;min-height:48px;margin-top:16px;background:#018FC7;border:0;border-radius:10px;color:white;font-weight:800}.error{color:#a22;background:#fff1f1;border:1px solid #efbbbb;padding:10px;border-radius:8px}.back{display:block;text-align:center;margin-top:18px;color:#087ca5;text-decoration:none;font-weight:700}
    </style>
</head>
<body>
    <main class="card">
        <img src="/images/logo-rodagest.png" alt="RODAGEST">
        <h1>Verifica tu identidad</h1>
        <p class="hint">Abre Google Authenticator e ingresa el código actual de seis dígitos.</p>
        @if($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
        <form method="POST" action="{{ route('2fa.verify', [], false) }}">
            @csrf
            <label for="code">Código de autenticación</label>
            <input class="field" id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus>
            <button class="btn">Verificar y continuar</button>
        </form>
        <a class="back" href="{{ route('login', [], false) }}">Volver al inicio de sesión</a>
    </main>
</body>
</html>
