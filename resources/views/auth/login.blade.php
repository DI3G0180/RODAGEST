<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FBF1E5">
    <title>Iniciar sesión · RODAGEST</title>
    <style>
        :root{color-scheme:light;--primary:#018FC7;--secondary:#83C0DF;--border:#B1D0E2;--soft:#DBE7F3;--cream:#FBF1E5;--ink:#183647;--muted:#607985}
        *{box-sizing:border-box}html,body{min-height:100%;margin:0}body{min-height:100vh;display:grid;place-items:center;padding:24px 16px;background:var(--cream);color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
        .card{width:min(100%,440px);padding:clamp(24px,6vw,42px);border:1px solid var(--border);border-radius:22px;background:#fff;box-shadow:0 18px 55px #17445b16}
        .logo{display:block;width:min(190px,62%);height:88px;margin:0 auto 18px;object-fit:contain;mix-blend-mode:multiply}
        h1{margin:0;text-align:center;font-size:clamp(24px,6vw,30px);line-height:1.2;letter-spacing:-.035em}.intro{margin:9px 0 25px;text-align:center;color:var(--muted);font-size:14px;line-height:1.5}
        .alert{margin:0 0 18px;padding:12px 14px;border:1px solid #efbbbb;border-radius:11px;background:#fff1f1;color:#9e2e2e;font-size:13px;line-height:1.5}.alert ul{margin:0;padding-left:18px}.alert li+li{margin-top:3px}
        .field{margin:15px 0}.field label{display:block;margin:0 0 7px;font-size:13px;font-weight:750}.field input{display:block;width:100%;height:48px;padding:0 13px;border:1px solid var(--border);border-radius:10px;background:#fff;color:var(--ink);font:inherit;font-size:14px;outline:none}.field input:focus{border-color:var(--primary);box-shadow:0 0 0 3px #018FC71c}.password-wrap{position:relative}.password-wrap input{padding-right:80px}.toggle{position:absolute;top:7px;right:7px;min-height:34px;padding:0 10px;border:0;border-radius:8px;background:var(--soft);color:#05668e;font-weight:700;font-size:12px;cursor:pointer}.options{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin:12px 0 19px;font-size:12px}.remember{display:flex;align-items:center;gap:7px;color:var(--muted)}.remember input{accent-color:var(--primary)}a{color:#087ca5;text-decoration:none;font-weight:700}a:hover{text-decoration:underline}.submit{width:100%;min-height:48px;border:0;border-radius:10px;background:var(--primary);color:#fff;font-size:14px;font-weight:800;cursor:pointer}.submit:hover{background:#0076a6}.divider{display:flex;align-items:center;gap:12px;margin:23px 0 12px;color:#70858f;font-size:10px;font-weight:800;letter-spacing:.09em;text-transform:uppercase}.divider:before,.divider:after{height:1px;flex:1;background:var(--border);content:""}.demos{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.demo{min-width:0;min-height:42px;padding:8px 10px;border:1px solid var(--border);border-radius:9px;background:#f8fbfd;color:#17536d;text-align:left;font-size:12px;font-weight:750;cursor:pointer}.demo:hover,.demo:focus-visible{border-color:var(--primary);background:#edf8fc}.demo small{display:block;margin-top:3px;color:var(--muted);font-size:10px;font-weight:500}.footnote{margin:22px 0 0;padding-top:15px;border-top:1px solid #B1D0E288;text-align:center;color:#78909b;font-size:11px}
        @media(max-width:380px){body{padding:12px}.card{padding:23px 18px;border-radius:17px}.logo{height:72px;margin-bottom:14px}.intro{font-size:13px}.options{font-size:11px}.demo{font-size:11px;padding:7px}}
        @media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important;transition:none!important}}
    </style>
</head>
<body>
<main class="card">
    <img class="logo" src="/images/logo-rodagest.png" alt="Logotipo RODAGEST">
    <h1>Bienvenido a RODAGEST</h1>
    <p class="intro">Inicia sesión para continuar con la operación de tu flota.</p>

    @if($errors->any())<div class="alert" role="alert" aria-live="assertive"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if(session('status'))<div class="alert" role="status" style="border-color:#a8dfc1;background:#effcf5;color:#176b45">{{ session('status') }}</div>@endif

    <form method="POST" action="{{ route('login', [], false) }}">
        @csrf
        <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="usuario@rodagest.com"></div>
        <div class="field"><label for="password">Contraseña</label><div class="password-wrap"><input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Tu contraseña"><button class="toggle" type="button" id="toggle-password" aria-label="Mostrar contraseña">Mostrar</button></div></div>
        <div class="options"><label class="remember"><input type="checkbox" name="remember"> Recordar sesión</label>@if(Route::has('password.request'))<a href="{{ route('password.request', [], false) }}">¿Olvidaste tu contraseña?</a>@endif</div>
        <button class="submit" type="submit">Iniciar sesión</button>
    </form>

    <div class="divider">Acceso de demostración</div>
    <div class="demos" aria-label="Usuarios de prueba">
        <button class="demo" type="button" data-email="admin@rodagest.com">Administrador<small>Supervisión y flota</small></button>
        <button class="demo" type="button" data-email="inspector@rodagest.com">Inspector<small>Seguridad técnica</small></button>
        <button class="demo" type="button" data-email="operador@rodagest.com">Operador<small>Bitácora de turno</small></button>
        <button class="demo" type="button" data-email="limpieza@rodagest.com">Limpieza<small>Sanitización de unidades</small></button>
    </div>
    <p class="footnote">Sistema Integral de Gestión de Flotas · RODAGEST</p>
</main>
<script>
    const password=document.getElementById('password');
    document.getElementById('toggle-password').addEventListener('click',event=>{const showing=password.type==='password';password.type=showing?'text':'password';event.currentTarget.textContent=showing?'Ocultar':'Mostrar';event.currentTarget.setAttribute('aria-label',showing?'Ocultar contraseña':'Mostrar contraseña')});
    document.querySelectorAll('.demo').forEach(button=>button.addEventListener('click',()=>{document.getElementById('email').value=button.dataset.email;password.value='password123';password.focus()}));
</script>
</body>
</html>
