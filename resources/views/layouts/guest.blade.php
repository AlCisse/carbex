<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Carbex') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <a href="{{ route('home') }}" class="flex justify-center">
                <x-application-logo class="h-12 w-auto" />
            </a>
            @isset($heading)
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                {{ $heading }}
            </h2>
            @endisset
            @isset($subheading)
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ $subheading }}
            </p>
            @endisset
        </div>

        <!-- Content -->
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                {{ $slot }}
            </div>

            @isset($footer)
            <div class="mt-6">
                {{ $footer }}
            </div>
            @endisset
        </div>

        <!-- Language Switcher -->
        <div class="mt-8 flex justify-center space-x-4 text-sm text-gray-500">
            <a href="?lang=fr" class="{{ app()->getLocale() === 'fr' ? 'font-semibold text-green-600' : 'hover:text-gray-700' }}">
                Francais
            </a>
            <span>|</span>
            <a href="?lang=de" class="{{ app()->getLocale() === 'de' ? 'font-semibold text-green-600' : 'hover:text-gray-700' }}">
                Deutsch
            </a>
            <span>|</span>
            <a href="?lang=en" class="{{ app()->getLocale() === 'en' ? 'font-semibold text-green-600' : 'hover:text-gray-700' }}">
                English
            </a>
        </div>
    </div>

    @livewireScripts
</body>
</html>
