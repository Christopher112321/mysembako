<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MySembako</title>
    {{-- Vite React preamble for @vitejs/plugin-react (required for React Refresh) --}}
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @if (app()->environment('local'))
        @if (!file_exists(public_path('hot')))
            <script>
                console.warn('Vite dev server tidak berjalan. Silakan jalankan: npm run dev');
            </script>
        @endif
    @endif
</head>
<body>
    <div id="app"></div>
</body>
</html>

