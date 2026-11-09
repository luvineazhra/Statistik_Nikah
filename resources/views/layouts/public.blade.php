<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $title ?? 'Statistik Pernikahan dan Perceraian Indonesia • Portal Publik' }}</title>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
