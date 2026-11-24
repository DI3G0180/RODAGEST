<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RODAGEST — @yield('title', 'Gestión de flotas')</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>h1,h2{color:#05161A}thead{background:#072E33!important;color:#fff!important}thead th{color:#fff!important}</style>
</head>
<body class="min-h-screen bg-[#F4F9FB] font-sans text-[#05161A] antialiased">
    @include('partials.role-menu')
    @if(session('success') || session('error') || (session('status') && !in_array(session('status'), ['two-factor-enabled', 'two-factor-disabled', 'profile-updated', 'password-updated', 'verification-link-sent'], true)))
        <div class="mx-auto w-full max-w-7xl space-y-2 px-4 pt-3 sm:px-6" aria-live="polite">
            @if(session('success'))<div x-data="{ visible: true }" x-show="visible" x-transition.opacity x-init="setTimeout(() => visible = false, 5000)" class="flash-message rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800" role="status">{{ session('success') }}</div>@endif
            @if(session('status') && !in_array(session('status'), ['two-factor-enabled', 'two-factor-disabled', 'profile-updated', 'password-updated', 'verification-link-sent'], true))<div x-data="{ visible: true }" x-show="visible" x-transition.opacity x-init="setTimeout(() => visible = false, 5000)" class="flash-message rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800" role="status">{{ session('status') }}</div>@endif
            @if(session('error'))<div x-data="{ visible: true }" x-show="visible" x-transition.opacity x-init="setTimeout(() => visible = false, 6000)" class="flash-message rounded-xl border border-red-200 bg-red-50 p-3 text-sm font-medium text-red-800" role="alert">{{ session('error') }}</div>@endif
        </div>
    @endif
    <main class="min-w-0">@yield('content')</main>
</body>
</html>
