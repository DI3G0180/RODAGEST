@extends('layouts.app')

@section('title', 'Ficha ' . $unidad->economico . ' · RODAGEST')

@section('content')
<div class="mx-auto w-full max-w-6xl space-y-5 px-4 py-5 sm:px-6 sm:py-7">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>@endif

    <a href="{{ route('dashboard', [], false) }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg px-2 text-sm font-semibold text-[#018FC7] hover:bg-[#DBE7F3]">← Volver al panel</a>

    <section class="overflow-hidden rounded-3xl border border-[#B1D0E2] bg-white shadow-sm">
        <div class="grid gap-5 border-b border-[#DBE7F3] p-5 sm:p-7 lg:grid-cols-[minmax(0,1fr)_220px] lg:items-center">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[.16em] text-[#018FC7]">Ficha digital de unidad</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="break-words text-3xl font-extrabold tracking-tight text-slate-800 sm:text-4xl">{{ $unidad->economico }}</h1>
                    @php($estadoLabel = ['operativa'=>'Operativa','activo'=>'Operativa','en_taller'=>'En taller','mantenimiento'=>'En taller','en_limpieza'=>'En limpieza','fuera_servicio'=>'Fuera de servicio'][$unidad->estado] ?? ucfirst(str_replace('_', ' ', $unidad->estado ?? 'Sin estado')))
                    <span class="rounded-full bg-[#DBE7F3] px-3 py-1.5 text-xs font-bold text-[#176B91]">{{ $estadoLabel }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-600">Historial operativo e información actual de la unidad.</p>
            </div>
            <div id="qr-print-card" class="qr-print flex items-center gap-4 rounded-2xl bg-[#FBF1E5] p-4 lg:flex-col lg:gap-2 lg:text-center">
                <div id="unit-qr" data-qr-url="{{ route('unidades.show', [$unidad->id], false) }}" class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl border border-[#B1D0E2] bg-white p-2" aria-label="Código QR de {{ $unidad->economico }}"></div>
                <div class="min-w-0"><p class="text-sm font-bold text-slate-800">Acceso por QR</p><p class="mt-1 text-xs text-slate-600">Escanéalo para abrir esta ficha.</p><button type="button" onclick="window.print()" class="mt-2 inline-flex min-h-10 items-center rounded-lg border border-[#B1D0E2] bg-white px-3 text-xs font-bold text-[#018FC7] print:hidden">Imprimir QR</button></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 sm:gap-4 sm:p-6 lg:grid-cols-5">
                @foreach([['Modelo', $unidad->modelo], ['Odómetro', $unidad->odometro ? number_format($unidad->odometro) . ' km' : null], ['Operador', $unidad->operador?->name], ['Próximo servicio', $unidad->proximo_mantenimiento_fecha?->format('d/m/Y')], ['Servicio por km', $unidad->proximo_mantenimiento_odometro ? number_format($unidad->proximo_mantenimiento_odometro) . ' km' : null]] as [$label, $value])
                <article class="min-w-0 rounded-2xl bg-[#DBE7F3] p-3 sm:p-4"><p class="text-xs font-medium text-slate-600">{{ $label }}</p><p class="mt-1 break-words text-sm font-bold text-slate-800 sm:text-base">{{ $value ?: '—' }}</p></article>
            @endforeach
        </div>
        @if(auth()->user()->role === 'admin')<div class="px-4 pb-5 sm:px-6"><a href="{{ route('unidades.edit', [$unidad->id], false) }}" class="inline-flex min-h-11 items-center rounded-xl border border-[#B1D0E2] px-4 text-sm font-bold text-[#018FC7]">Actualizar unidad y próximo servicio</a></div>@endif
    </section>

    <section class="rounded-3xl border border-[#B1D0E2] bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#018FC7]">Bitácora</p><h2 class="mt-1 text-xl font-extrabold sm:text-2xl">Actividad reciente</h2></div>
            <span class="text-sm text-slate-500">Últimos 40 movimientos</span>
        </div>

        <div class="mt-5 space-y-3">
            @forelse($eventos as $evento)
                <article class="grid min-w-0 gap-3 rounded-2xl border border-[#DBE7F3] p-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
                    <div class="flex min-w-0 gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#DBE7F3] text-xs font-extrabold text-[#018FC7]">{{ mb_substr($evento['tipo'], 0, 2) }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><span class="text-xs font-bold uppercase tracking-wide text-[#018FC7]">{{ $evento['tipo'] }}</span><span class="rounded-full bg-[#FBF1E5] px-2.5 py-1 text-xs font-semibold text-slate-700">{{ str_replace('_', ' ', $evento['estado'] ?: 'Registrado') }}</span></div>
                            <h3 class="mt-1 break-words font-bold text-slate-800">{{ $evento['titulo'] }}</h3>
                            @if($evento['detalle'])<p class="mt-1 whitespace-pre-line break-words text-sm text-slate-600">{{ $evento['detalle'] }}</p>@endif
                            @if($evento['responsable'])<p class="mt-2 text-xs text-slate-500">Responsable: {{ $evento['responsable'] }}</p>@endif
                            @if($evento['foto'])<img src="{{ '/storage/' . $evento['foto'] }}" alt="Evidencia de {{ $evento['tipo'] }}" loading="lazy" class="mt-3 max-h-56 max-w-full rounded-xl border border-[#DBE7F3] object-contain">@endif
                        </div>
                    </div>
                    <time class="pl-[3.25rem] text-xs text-slate-500 sm:pl-0 sm:text-right">{{ $evento['fecha']?->format('d/m/Y H:i') ?? 'Fecha no disponible' }}</time>
                </article>
            @empty
                <div class="rounded-2xl bg-[#FBF1E5] p-6 text-center"><p class="font-bold text-slate-800">Aún no hay actividad</p><p class="mt-1 text-sm text-slate-600">Cuando se registre una inspección, aseo, turno o incidencia aparecerá aquí.</p></div>
            @endforelse
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" defer></script>
<script>
    window.addEventListener('load', () => {
        const element = document.getElementById('unit-qr');
        if (element && window.QRCode) {
            new QRCode(element, { text: `${window.location.origin}${element.dataset.qrUrl}`, width: 88, height: 88, colorDark: '#17324D', colorLight: '#FFFFFF', correctLevel: QRCode.CorrectLevel.M });
        } else if (element) {
            element.innerHTML = '<span class="text-center text-[10px] text-slate-500">QR no disponible<br>Abre el enlace de la ficha</span>';
        }
    });
</script>
<style>
@media print { body * { visibility: hidden !important; } #qr-print-card, #qr-print-card * { visibility: visible !important; } #qr-print-card { position: absolute; inset: 20% auto auto 50%; transform: translateX(-50%); width: 240px; background: #fff !important; justify-content: center; border: 1px solid #B1D0E2; } }
</style>
@endsection
