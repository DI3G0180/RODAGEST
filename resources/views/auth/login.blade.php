<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#083344">
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>RODAGEST — Iniciar sesión</title>
    <style>
        :root{color-scheme:light;--petroleum:#083344;--teal:#0E7490;--ink:#0f2933;--muted:#526875;--line:#d8e5ea}
        *{box-sizing:border-box}html,body{min-height:100%;margin:0}body{min-height:100vh;background:#f4f8fa;color:var(--ink);font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
        .login-shell{min-height:100vh;display:flex;flex-direction:column;justify-content:center;padding:28px 16px}.login-brand{width:min(100%,480px);margin:0 auto;padding:32px 24px 48px;border-radius:26px 26px 0 0;background:linear-gradient(135deg,#083344,#0E7490);color:#fff;text-align:center;box-shadow:0 16px 42px rgba(8,51,68,.14)}
        .brand-logo{width:126px;height:72px;object-fit:contain;padding:8px;border-radius:16px;background:#fff;mix-blend-mode:normal}.brand-title{margin:12px 0 0;font-size:24px;font-weight:800;letter-spacing:.12em}.brand-subtitle{margin:5px 0 0;color:#e6f7fb;font-size:13px;font-weight:500}
        .login-card{position:relative;width:min(100%,480px);margin:-20px auto 0;padding:30px clamp(22px,6vw,40px) 28px;border:1px solid var(--line);border-radius:24px 24px 22px 22px;background:#fff;box-shadow:0 20px 50px rgba(8,51,68,.13)}
        h1{margin:0;text-align:center;color:var(--petroleum);font-size:clamp(23px,6vw,28px);line-height:1.25;letter-spacing:-.035em}.intro{margin:8px 0 24px;text-align:center;color:var(--muted);font-size:14px;line-height:1.5}
        .alert{margin:0 0 17px;padding:12px 14px;border:1px solid #efbcbc;border-radius:11px;background:#fff3f3;color:#9e2e2e;font-size:13px;line-height:1.5}.alert ul{margin:0;padding-left:18px}.alert li+li{margin-top:3px}
        .field{margin:15px 0}.field label{display:block;margin:0 0 7px;color:var(--petroleum);font-size:13px;font-weight:750}.field input{display:block;width:100%;height:49px;padding:0 13px;border:1px solid #b9d4df;border-radius:11px;background:#fff;color:var(--ink);font:inherit;font-size:16px;outline:none}.field input:focus{border-color:var(--teal);box-shadow:0 0 0 3px rgba(14,116,144,.14)}.password-wrap{position:relative}.password-wrap input{padding-right:54px}.password-wrap input::-ms-reveal,.password-wrap input::-ms-clear{display:none}.toggle{position:absolute;top:4px;right:4px;display:grid;width:41px;height:41px;place-items:center;border:0;border-radius:9px;background:transparent;color:var(--teal);cursor:pointer}.toggle:hover{background:#eff7f9}.toggle svg{width:20px;height:20px}
        .options{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin:13px 0 19px;font-size:12px}.remember{display:flex;align-items:center;gap:7px;color:var(--muted)}.remember input{accent-color:var(--teal)}.submit{width:100%;min-height:50px;border:0;border-radius:11px;background:var(--teal);color:#fff;font-size:15px;font-weight:800;cursor:pointer;transition:background .18s ease}.submit:hover{background:var(--petroleum)}.footnote{margin:20px 0 0;padding-top:15px;border-top:1px solid #d8e5ea;text-align:center;color:var(--muted);font-size:11px;line-height:1.55}
        @media(max-width:380px){.login-shell{padding:14px 12px}.login-brand{padding:24px 18px 42px}.login-card{padding:25px 18px 23px}.brand-logo{width:110px;height:64px}.intro{font-size:13px}}
        @media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;transition:none!important}}
    </style>
</head>
<body>
@include('components.splash-screen')
<div class="login-shell">
    <header class="login-brand">
        <img class="brand-logo" src="/images/logo-rodagest.png" alt="Logotipo RODAGEST">
        <p class="brand-title">RODAGEST</p>
        <p class="brand-subtitle">Control y Gestión Inteligente de Flotas</p>
    </header>
    <main class="login-card">
        <h1>Bienvenido de nuevo</h1>
        <p class="intro">Inicia sesión para continuar con la operación de tu flota.</p>

        @if($errors->any())<div class="alert" role="alert" aria-live="assertive"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @if(session('status'))<div class="alert" role="status" style="border-color:#a8dfc1;background:#effcf5;color:#176b45">{{ session('status') }}</div>@endif

        <form method="POST" action="{{ route('login', [], false) }}">
            @csrf
            <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="usuario@rodagest.com"></div>
            <div class="field"><label for="password">Contraseña</label><div class="password-wrap"><input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Tu contraseña"><button class="toggle" type="button" id="toggle-password" aria-label="Alternar visibilidad de contraseña"><svg id="password-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
            <div class="options"><label class="remember"><input type="checkbox" name="remember"> Recordar sesión</label></div>
            <button class="submit" type="submit">Iniciar sesión</button>
        </form>
        <p class="footnote" style="border-top:0;margin-top:14px;padding-top:0">¿Perdiste o bloqueaste tus credenciales? Solicita al Administrador que restablezca tu contraseña.</p>
        <p class="footnote">Sistema Integral de Gestión de Flotas · RODAGEST</p>
    </main>
</div>
<script>
    (function(){
        var password=document.getElementById('password');
        var togglePassword=document.getElementById('toggle-password');
        if(!password||!togglePassword)return;
        togglePassword.addEventListener('click',function(){
            var showing=password.type==='password';
            password.type=showing?'text':'password';
            togglePassword.setAttribute('aria-label',showing?'Ocultar contraseña':'Mostrar contraseña');
            document.getElementById('password-eye').innerHTML=showing
                ? '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 002.8 2.8"/><path d="M9.9 5.2A10.9 10.9 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.1 3.9M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 004.1-.8"/>'
                : '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
        });
    })();
</script>
</body>
</html>
