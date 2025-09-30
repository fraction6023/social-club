<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'App') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">
    <div class="min-h-screen">

        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white border-b">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @else
            <header class="bg-white border-b">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800">الرئيسية</h2>
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-black/5">
                    {{ $slot }}
                </div>
            </div>
        </main>

        {{-- Footer بسيط اختياري --}}
        <footer class="text-xs text-gray-500 py-6 text-center">
            © {{ date('Y') }} — {{ config('app.name') }}
        </footer>
    </div>
</body>
</html>
