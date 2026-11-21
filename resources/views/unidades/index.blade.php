@extends('layouts.app')
@section('title', 'Unidades de flota')
@section('content')
<div class="mx-auto max-w-7xl space-y-5 p-4 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-extrabold">Catálogo de unidades</h1><p class="text-sm text-slate-600">Administración de flota y asignaciones.</p></div><a href="{{ route('unidades.create', [], false) }}" class="rounded-lg bg-[#018FC7] px-4 py-2 font-bold text-white">Nueva unidad</a></div>
    @if(session('success'))<p class="rounded-lg bg-emerald-50 p-3 text-emerald-800">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="rounded-lg bg-red-50 p-3 text-red-800">{{ session('error') }}</p>@endif
    <section class="overflow-hidden rounded-2xl border border-[#B1D0E2] bg-white">
        <div class="space-y-3 p-3 md:hidden">
            @forelse($unidades as $unidad)
                <article class="rounded-2xl border border-[#DBE7F3] p-4">
                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><a class="break-words text-lg font-extrabold text-[#018FC7]" href="{{ route('unidades.show', [$unidad->id], false) }}">{{ $unidad->economico }}</a></div><span class="shrink-0 rounded-full bg-[#DBE7F3] px-2.5 py-1 text-xs font-semibold">{{ str_replace('_',' ',$unidad->estado ?? 'activa') }}</span></div>
                    <p class="mt-3 text-sm text-slate-600">Operador: <span class="font-semibold text-slate-800">{{ $unidad->operador?->name ?? 'Sin asignar' }}</span></p>
                    <div class="mt-3 flex flex-wrap gap-2"><a class="inline-flex min-h-10 items-center rounded-lg bg-[#DBE7F3] px-3 text-sm font-bold text-[#018FC7]" href="{{ route('unidades.show', [$unidad->id], false) }}">Ver ficha y QR</a><a class="inline-flex min-h-10 items-center rounded-lg border border-[#B1D0E2] px-3 text-sm font-semibold text-slate-700" href="{{ route('unidades.edit', [$unidad->id], false) }}">Editar</a></div>
                </article>
            @empty
                <p class="p-6 text-center text-slate-500">No hay unidades registradas.</p>
            @endforelse
        </div>
        <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[700px] text-left text-sm"><thead class="bg-[#DBE7F3]"><tr><th class="p-3">Económico</th><th class="p-3">Modelo</th><th class="p-3">Odómetro</th><th class="p-3">Operador</th><th class="p-3">Estado</th><th class="p-3">Acciones</th></tr></thead><tbody>
        @forelse($unidades as $unidad)<tr class="border-t border-[#DBE7F3]"><td class="p-3 font-bold"><a class="text-[#018FC7] hover:underline" href="{{ route('unidades.show', [$unidad->id], false) }}">{{ $unidad->economico }}</a></td><td class="p-3">{{ $unidad->modelo ?? '—' }}</td><td class="p-3">{{ $unidad->odometro ?? '—' }}</td><td class="p-3">{{ $unidad->operador?->name ?? '—' }}</td><td class="p-3">{{ str_replace('_',' ',$unidad->estado ?? 'activa') }}</td><td class="p-3"><div class="flex gap-3"><a class="text-[#018FC7]" href="{{ route('unidades.show', [$unidad->id], false) }}">Ficha / QR</a><a class="text-[#018FC7]" href="{{ route('unidades.edit', [$unidad->id], false) }}">Editar</a><form action="{{ route('unidades.destroy', [$unidad->id], false) }}" method="POST">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('¿Eliminar unidad {{ $unidad->economico }}?')">Eliminar</button></form></div></td></tr>@empty<tr><td colspan="6" class="p-8 text-center text-slate-500">No hay unidades registradas.</td></tr>@endforelse
        </tbody></table></div><div class="p-4">{{ $unidades->links() }}</div>
    </section>
</div>
@endsection
