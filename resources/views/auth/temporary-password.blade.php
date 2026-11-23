<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RODAGEST — Actualiza tu contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="grid min-h-screen place-items-center bg-[#FBF1E5] p-4 font-sans text-slate-800">
    <main class="w-full max-w-lg rounded-3xl border border-[#B1D0E2] bg-white p-6 shadow-xl sm:p-9">
        <img src="/images/logo-rodagest.png" alt="RODAGEST" class="mx-auto h-16 w-auto object-contain [mix-blend-mode:multiply]">
        <h1 class="mt-5 text-center text-2xl font-extrabold">Actualiza tu contraseña personal</h1>
        <p class="mt-2 text-center text-sm text-slate-600">Por seguridad, cambia la clave temporal antes de usar el sistema.</p>
        @if($errors->any())<div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <form method="POST" action="{{ route('password.temporary.update', [], false) }}" class="mt-6 space-y-4" x-data="{password:'',visible:false,checks(){return [this.password.length>=8,/[A-Z]/.test(this.password),/[a-z]/.test(this.password),/[0-9]/.test(this.password),/[^A-Za-z0-9]/.test(this.password)]}}">@csrf @method('PUT')
            <label class="block text-sm font-semibold">Nueva contraseña<input name="password" x-model="password" :type="visible?'text':'password'" autocomplete="new-password" required class="mt-1 w-full rounded-xl border border-[#B1D0E2] px-4 py-3 focus:border-[#018FC7] focus:outline-none focus:ring-2 focus:ring-[#018FC7]/20"></label>
            <button type="button" @click="visible=!visible" class="text-sm font-semibold text-[#018FC7]" x-text="visible?'Ocultar contraseña':'Mostrar contraseña'"></button>
            <ul class="grid grid-cols-2 gap-2 text-xs text-slate-500 sm:grid-cols-3"><template x-for="(label,index) in ['Longitud','Mayúscula','Minúscula','Número','Símbolo']" :key="label"><li :class="checks()[index]?'font-bold text-emerald-700':''"><span x-text="checks()[index]?'✓':'○'"></span> <span x-text="label"></span></li></template></ul>
            <label class="block text-sm font-semibold">Confirma la nueva contraseña<input name="password_confirmation" type="password" autocomplete="new-password" required class="mt-1 w-full rounded-xl border border-[#B1D0E2] px-4 py-3 focus:border-[#018FC7] focus:outline-none focus:ring-2 focus:ring-[#018FC7]/20"></label>
            <button class="min-h-12 w-full rounded-xl bg-[#018FC7] px-4 font-bold text-white hover:bg-sky-700">Guardar contraseña y continuar</button>
        </form>
    </main>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
