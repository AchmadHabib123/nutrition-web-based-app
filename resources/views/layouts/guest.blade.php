<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .bg-custom-dark-purple {
                background-color: #201a24; /* A dark purple shade for the right side */
            }
            .bg-custom-teal {
                background-color: #1ed7b6; /* The teal color for the button */
            }
            .text-custom-light-green {
                color: #55c8b5; /* The light green for links */
            }
            .bg-custom-dark-blue-left {
                background-color: #2b2b3a; /* Dark blue for the left panel */
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen grid grid-cols-2 sm:justify-center items-center pt-6 sm:pt-0 bg-custom-dark-blue-left">
            <div class="flex flex-col items-center justify-center p-8 bg-custom-dark-blue-left text-white text-center">
                <img src="{{ asset('storage/assets/login.png') }}" alt="Vibex Illustration" class="max-w-xs mx-auto mb-6">
                <p class="text-xl leading-relaxed max-w-sm">
                    Terwujudnya Upaya dan Pelayanan Kesehatan yang Berdayaguna, Berhasilguna dan Dipercaya
                </p>
            </div>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
