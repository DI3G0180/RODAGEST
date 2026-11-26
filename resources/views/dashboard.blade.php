<!doctype html>
<html lang="es" class="overflow-x-hidden">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <title>RODAGEST — Panel operativo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F4F9FB] text-[#05161A] antialiased">
@php($user = $user ?? auth()->user())
@php($userRole = $userRole ?? $user?->role ?? '')
@php($alertas = $alertas ?? collect())
@php($empleados = $empleados ?? collect())
@php($unidades = $unidades ?? collect())
@php($inspecciones = $inspecciones ?? collect())
@php($aseos = $aseos ?? collect())
@php($reportes = $reportes ?? collect())
@php($unidadAsignada = $unidadAsignada ?? null)
@php($turnoAbierto = $turnoAbierto ?? null)
@php($liberacionUnidad = $liberacionUnidad ?? false)
@php($estadoFlota = array_merge(['operativa' => 0, 'en_taller' => 0, 'en_limpieza' => 0, 'fuera_servicio' => 0], $estadoFlota ?? []))
@php($kpis = array_merge(['unidades' => 0, 'empleados' => 0, 'inspeccionesHoy' => 0, 'inspeccionesCriticas' => 0, 'aseosCompletados' => 0, 'aseosHoy' => 0, 'aseosPendientes' => 0, 'inspeccionesPendientes' => 0, 'reportesAbiertos' => 0, 'reportesOperador' => 0, 'reportesPendientesOperador' => 0, 'recorridosHoy' => 0, 'reportesLimpiezaPendientes' => 0], $kpis ?? []))
@php($roleLabels = ['admin' => 'Administrador', 'inspector' => 'Inspector', 'operador' => 'Operador', 'limpieza' => 'Limpieza'])
@php($dashboardTitles = ['admin' => 'Administración', 'inspector' => 'Panel de inspección', 'operador' => 'Panel de operador', 'limpieza' => 'Panel de limpieza'])
@php($dashboardSubtitles = ['admin' => 'Resumen ejecutivo de la operación y la flota.', 'inspector' => 'Captura hallazgos y evidencia desde patio.', 'operador' => 'Consulta tu unidad, turno y reportes de ruta.', 'limpieza' => 'Consulta el avance de aseo y libera unidades con evidencia.'])
@php($primaryAction = match($userRole) {
    'admin' => ['label' => '+ Registrar unidad', 'url' => route('unidades.create', [], false)],
    'inspector' => ['label' => '+ Nueva inspección', 'url' => route('inspecciones.create', [], false)],
    'operador' => ['label' => 'Turno / checklist de hoy', 'url' => '#turno-operador'],
    'limpieza' => ['label' => 'Completar aseo 4/4 hoy', 'url' => '#aseo-rapido'],
    default => null,
})
@include('partials.role-menu')
<main class="mx-auto max-w-7xl space-y-4 overflow-x-hidden px-3 pb-20 pt-3 sm:space-y-6 sm:p-6 md:pb-8 lg:p-8">
    <header class="flex flex-col gap-4 border-b border-[#6DA5C0]/30 pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-widest text-[#0C7075]">{{ $roleLabels[$userRole] ?? 'Personal' }} · {{ now()->format('d/m/Y') }}</p>
            <h1 class="mt-1 text-2xl font-extrabold text-[#05161A] sm:text-3xl">{{ $dashboardTitles[$userRole] ?? 'Panel RODAGEST' }}</h1>
            <p class="mt-1 text-sm text-[#294D61]">{{ $dashboardSubtitles[$userRole] ?? 'Operación Línea 3 Mexibús.' }}</p>
        </div>
        @if($primaryAction)
            @if(isset($primaryAction['target']))
                <button type="button" onclick="document.getElementById('{{ $primaryAction['target'] }}')?.setAttribute('open','open'); document.getElementById('{{ $primaryAction['target'] }}')?.scrollIntoView({behavior:'smooth',block:'center'});" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F969C] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-[#0C7075]">{{ $primaryAction['label'] }}</button>
            @else
                <a href="{{ $primaryAction['url'] }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0F969C] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-[#0C7075]">{{ $primaryAction['label'] }}</a>
            @endif
        @endif
    </header>
    @if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if($alertas && $userRole !== 'admin')<section class="rounded-2xl border border-[#6DA5C0]/50 bg-white p-4 shadow-sm sm:p-5"><div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#0C7075]">Centro de alertas</p><h2 class="mt-1 text-lg font-extrabold">Pendientes operativos</h2></div><span class="rounded-full bg-[#F4F9FB] px-3 py-1 text-xs font-bold text-[#0C7075]">{{ count($alertas) }} {{ count($alertas) === 1 ? 'aviso' : 'avisos' }}</span></div><div class="mt-3 grid gap-2 sm:grid-cols-2">@foreach($alertas as $alerta)@php($alertaColor=['danger'=>'border-red-200 bg-red-50','warning'=>'border-amber-200 bg-amber-50','info'=>'border-[#6DA5C0]/50 bg-[#F4F9FB]'][$alerta['nivel']] ?? 'border-[#6DA5C0]/50 bg-white')<a href="{{ $alerta['ruta'] }}" class="block min-w-0 rounded-xl border p-3 transition hover:shadow-sm {{ $alertaColor }}"><span class="block break-words text-sm font-bold">{{ $alerta['titulo'] }}</span><span class="mt-1 block break-words text-xs text-[#294D61]">{{ $alerta['detalle'] }}</span></a>@endforeach</div></section>@endif

    @includeWhen($userRole === 'admin', 'dashboard.admin')
    @includeWhen($userRole === 'inspector', 'dashboard.inspector')
    @includeWhen($userRole === 'operador', 'dashboard.operador')
    @includeWhen($userRole === 'limpieza', 'dashboard.limpieza')
</main>
<style>h1,h2{color:#05161A}thead{background:#072E33!important;color:#fff!important}thead th{color:#fff!important}.field{width:100%;min-width:0;border:1px solid #6DA5C0;border-radius:.65rem;background:#fff;padding:.7rem .8rem;font-size:.9rem;outline:none}.field:focus{border-color:#0C7075;box-shadow:0 0 0 3px #0C707533}.metric{display:flex;height:100%;min-height:7rem;flex-direction:column;justify-content:space-between;border:1px solid rgb(109 165 192 / .5);border-top:4px solid #0C7075;border-radius:1rem;background:#fff;padding:.75rem;box-shadow:0 1px 2px rgb(5 22 26 / .08);transition:box-shadow .2s}.metric:hover{box-shadow:0 4px 12px rgb(5 22 26 / .12)}.metric>div:first-child>span{display:block;color:#294D61;font-size:.65rem;font-weight:700;line-height:1.35;letter-spacing:.06em;text-transform:uppercase}.metric b{display:block;color:#05161A;font-size:1.75rem;line-height:1.1;margin:.35rem 0}.metric-footer{display:flex;align-items:center;justify-content:space-between;gap:.5rem;border-top:1px solid rgb(109 165 192 / .25);padding-top:.45rem;font-size:.68rem}.metric-footer span{color:#294D61}.metric-footer a{flex-shrink:0;font-weight:600;color:#0C7075}.metric-footer a:hover{color:#05161A}@media(min-width:640px){.metric{min-height:9rem;padding:1.5rem}.metric>div:first-child>span{font-size:.75rem}.metric b{font-size:2.25rem;margin:.5rem 0}.metric-footer{padding-top:.75rem;font-size:.75rem}}</style>
</body>
</html>
