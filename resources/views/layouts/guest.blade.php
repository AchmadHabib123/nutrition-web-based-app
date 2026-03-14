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
        <link 
        rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
        />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 md:grid-cols-5 ">

            <!-- LEFT SIDE LOGIN -->
            <div class="flex items-center justify-center bg-gray-100 p-10 md:col-span-2">
        
                <div class="w-full max-w-md">
        
                    <h1 class="text-3xl font-bold mb-2">
                        Hi there!
                    </h1>
        
                    <p class="text-gray-500 mb-8">
                        Welcome to {{ config('app.name') }}.
                    </p>
        
                    {{ $slot }}
        
                </div>
        
            </div>
        
        
            <!-- RIGHT SIDE IMAGE -->
            <div class="hidden md:flex relative items-center justify-center text-white md:col-span-3">

                <div class="swiper w-full h-full">
            
                    <div class="swiper-wrapper">
            
                        <!-- SLIDE 1 -->
                        <div class="swiper-slide relative">
                            <img src="{{ asset('storage/assets/background.png') }}"
                                 class="absolute inset-0 w-full h-full object-cover">
            
                            <div class="absolute inset-0 bg-black/40"></div>
            
                            <div class="relative z-10 flex items-center h-full px-20">
                                <h2 class="text-4xl font-bold max-w-lg">
                                    Your laptop needs this new AI Technology
                                </h2>
                            </div>
                        </div>
            
                        <!-- SLIDE 2 -->
                        <div class="swiper-slide relative">
                            <img src="{{ asset('storage/assets/bg2.jpg') }}"
                                 class="absolute inset-0 w-full h-full object-cover">
            
                            <div class="absolute inset-0 bg-black/40"></div>
            
                            <div class="relative z-10 flex items-center h-full px-20">
                                <h2 class="text-4xl font-bold max-w-lg">
                                    Smart System For Modern Workflow
                                </h2>
                            </div>
                        </div>

                        <div class="swiper-slide relative">
                            <img src="{{ asset('storage/assets/background.png') }}"
                                 class="absolute inset-0 w-full h-full object-cover">
            
                            <div class="absolute inset-0 bg-black/40"></div>
            
                            <div class="relative z-10 flex items-center h-full px-20">
                                <h2 class="text-4xl font-bold max-w-lg">
                                    Your laptop needs this ne
                                </h2>
                            </div>
                        </div>
            
                    </div>
            
                    <!-- NAVIGATION BUTTON -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>

                    <!-- DOT PAGINATION -->
                    <div class="swiper-pagination"></div>
            
                </div>
            
            </div>
        
        </div>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    </body>
</html>
<script>
    const swiper = new Swiper('.swiper', {
    
        loop: true,
    
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
    
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
    
    });
</script>