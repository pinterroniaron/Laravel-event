<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Esemény kezelő</title>
    @vite('resources/css/app.css')
</head>

<body class="flex flex-col min-h-screen bg-blue-100">
    <header class="bg-blue-100 drop-shadow-2xl drop-shadow-blue-200">
        <nav class="max-w-6xl mx-auto">
            <div class="flex justify-between h-12 items-center">

                <a href="{{ route('home') }}" class="text-gray-700 text-xl hover:text-blue-500 font-bold">Event
                    Manager</a>
                <div class="space-x-6 hidden md:flex">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-500">Nyitó oldal</a>
                    <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-blue-500">Események</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-500">Rólunk</a>
                </div>
                <label for="mobile-menu-toggle" class="md:hidden cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
            </div>
            <input type="checkbox" id="mobile-menu-toggle" class="hidden peer" />
            <div class="hidden peer-checked:block md:hidden space-y-2 ms-2">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-500 block">Nyitó oldal</a>
                <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-blue-500 block">Események</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-500 block">Rólunk</a>
            </div>
        </nav>
    </header>

    <main class="flex-1 max-w-6xl w-full mx-auto mt-3 z-0">
        @yield('content')
    </main>

    <footer>
        lábrész
    </footer>
</body>

</html>
