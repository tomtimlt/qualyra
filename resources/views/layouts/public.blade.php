<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Assistant') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                            {{ config('app.name', 'AI Assistant') }}
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                    Déconnexion
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Commencer
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer class="bg-white border-t mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <p class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} {{ config('app.name', 'AI Assistant') }}. Tous droits réservés.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm">Mentions légales</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm">Confidentialité</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
