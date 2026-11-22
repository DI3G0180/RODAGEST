@extends('layouts.app')
@section('title', 'QR público de quejas')
@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-6 sm:px-6 sm:py-10">
    <section class="rounded-3xl border border-[#B1D0E2] bg-white p-5 shadow-sm sm:p-8">
        <a href="{{ route('quejas.index', [], false) }}" class="inline-flex min-h-10 items-center text-sm font-semibold text-[#018FC7]">← Volver a quejas</a>
        <div class="mt-3 grid gap-6 sm:grid-cols-[minmax(0,1fr)_240px] sm:items-center">
            <div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#018FC7]">Acceso externo</p><h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">QR para reportar quejas</h1><p class="mt-2 text-sm leading-6 text-slate-600">Cualquier persona puede escanearlo sin crear cuenta. El formulario pide ECO, ruta, tipo de queja y descripción; la foto y los datos de contacto son opcionales.</p><div class="mt-4 rounded-xl bg-[#FBF1E5] p-3"><p class="text-xs font-bold text-slate-700">Enlace público</p><a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="mt-1 block break-all text-sm font-semibold text-[#018FC7]">{{ $publicUrl }}</a></div></div>
            <div id="public-qr-card" class="flex flex-col items-center rounded-2xl border border-[#DBE7F3] bg-[#FBF1E5] p-4 text-center"><div id="public-qr" data-url="{{ $publicUrl }}" class="flex h-52 w-52 items-center justify-center rounded-xl bg-white p-2" aria-label="Código QR público para reportes"></div><p class="mt-3 font-extrabold text-slate-800">Escanea para reportar</p><p class="mt-1 text-xs text-slate-600">RODAGEST · Equipo rodante</p><button type="button" onclick="window.print()" class="mt-3 inline-flex min-h-11 items-center rounded-xl bg-[#018FC7] px-4 text-sm font-bold text-white print:hidden">Imprimir QR</button></div>
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" defer></script>
<script>window.addEventListener('load',()=>{const el=document.getElementById('public-qr');if(el&&window.QRCode){new QRCode(el,{text:el.dataset.url,width:192,height:192,colorDark:'#17324D',colorLight:'#FFFFFF',correctLevel:QRCode.CorrectLevel.M})}else if(el){el.textContent='No se pudo cargar el QR. Abre el enlace y vuelve a intentarlo.'}})</script>
<style>@media print{body *{visibility:hidden!important}#public-qr-card,#public-qr-card *{visibility:visible!important}#public-qr-card{position:absolute;top:15%;left:50%;transform:translateX(-50%);width:280px;background:white!important}}</style>
@endsection
