<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/nyc-logo_orig.png" type="image/x-icon">

    <title>NYC PYDI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="build/assets/app-D9BebNPI.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="build/assets/app-DVFseihA.css">

    @livewireStyles
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .left-side{
            width: 50%;
        }

        .right-side {
            height: 100%;
            width: 100%;
            overflow: visible;
            position: absolute;
            top: 0;
            right: 0;
        }

        .right-side img {
            position: absolute;
            right: 0;
            height: 100%;
            bottom: 0;
            z-index: 1;
        }

        .login-logo {
            position: relative;
            z-index: 1;
        }

        .main-container {
            overflow-x: hidden;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .animate-slide-in-right {
            transform: translateX(100%);
            animation: slideInRight 0.5s ease-out forwards;
        }

        .animate-slide-in-right-delay {
            transform: translateX(100%);
            animation: slideInRight 0.5s ease-out 0.15s forwards;
        }

        .right-side-content {
            transform: translateX(100%);
        }

        @media (max-width: 768px){
            .left-side{
                width: 100%;
            }   
            
            .right-side img {
                right: -200px;
            }
        }
    </style>
</head>

<body class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400">

     <main class="bg-gradient-to-br from-white to-gray-100 main-container">
        <div class="absolute top-0 left-0 flex w-full h-full bg-cover bg-center bg-no-repeat" style="background-image: url(images/bg.jpg); background-color: rgba(255, 255, 255, 0.5); background-blend-mode: overlay;">
        </div>  
        <div class="relative flex overflow-hidden justify-center lg:justify-between" style="z-index: 99">

            <!-- Content -->
            <div class="left-side">
                <div class="min-h-[100dvh] h-full flex flex-col after:flex-1">

                    <!-- Header -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <!-- Logo -->
                            <a class="block" href="{{ route('dashboard') }}">
                                <img src="/images/nyc_logo.png" alt="logo" class="h-12">
                            </a>
                        </div>
                    </div>

                    <div class="max-w-sm mx-auto w-full px-4 py-8">
                        {{ $slot }}

                        <p class="text-sm text-center text-gray-600">Don't have an account? <a href="register" class="text-blue-500 hover:text-blue-600">SIGN UP</a></p>
                    </div>

                </div>
            </div>

            <!-- Image -->
            <div class="hidden sm:block md:hidden lg:block absolute top-0 bottom-0 right-0 sm:w-0 md:w-0 lg:w-1/2 overflow-hidden right-side-content" aria-hidden="true">
                <div class="flex items-center justify-center w-full h-full login-logo animate-slide-in-right-delay">
                    <div class="flex items-center justify-center bg-white/80 backdrop-blur-md border border-white/90 rounded-full shadow-xl" style="width: 250px; height: 250px;">
                        <img class="object-contain" src="{{ asset('/images/nyc_logo.png') }}" style="width: 220px" alt="Authentication image" />
                    </div>
                </div>
            </div>

        </div>

        <div class="right-side animate-slide-in-right">
            <img src="/images/Vector.png" alt="login bg" >
        </div>
    </main>

    <script defer src="{{ asset('build/assets/app-DEoBNXZR.js') }}"></script>
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelector('.right-side-content').classList.add('animate-slide-in-right');
            }, 50);
        });
    </script>
</body>

</html>
