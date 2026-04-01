<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-900 text-gray-100 antialiased font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        @auth
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden"></div>

            <x-sidebar />
        @endauth

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            @auth
                <x-header />
            @endauth

            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 @guest flex flex-col items-center justify-center @endguest p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireScripts
    <script>
        function initIcons() { lucide.createIcons(); }
        initIcons();
        document.addEventListener('livewire:navigated', initIcons);
    </script>
</body>

</html>