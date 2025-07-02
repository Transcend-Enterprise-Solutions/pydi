<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/beahoa-logo.png" type="image/x-icon">

    <title>PYDI</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="build/assets/app-D9BebNPI.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="build/assets/app-DDRs7vCM.css">

    @livewireStyles
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .right-side-login {
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: absolute;
            top: 0;
            right: 0;
        }

        .right-side-login img {
            position: absolute;
            height: 100%;
            right: 0;
            z-index: 1;
        }

        .right-side-login div {
            height: 100%;
            width: 50%;
            right: 0;
            top: 0;
            position: absolute;
            background: #004AAD;
            z-index: 0;
        }

        .login-logo {
            position: relative;
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

        @media (max-width: 768px) {
            .login-bg {
                object-fit: cover !important;
                object-position: top;
            }
        }

        @media (max-width: 1024px) and (min-width:768px) {
            .login-bg {
                object-fit: cover !important;
                object-position: top;
            }
        }
    </style>
</head>

<body class="font-inter antialiased text-slate-600 dark:text-slate-400">

    <main class="main-container">

        <div class="relative flex">

            <!-- Left Content -->
            <div class="w-full md:w-1/2 relative z-10 overflow-visible">
                <div class="min-h-[100dvh] h-full flex flex-col items-center justify-center">
                    <img src="images/login-bg.png" alt="beahoa" class="w-full h-full absolute login-bg">

                    <div class="flex relative z-10 mb-4 sm:hidden">
                        <div class="flex items-center justify-center h-16 px-4 sm:px-6 lg:px-8">
                            <img src="images/beahoa-logo-white.png" alt="logo" style="width: 200px;">
                        </div>
                    </div>

                    <div class="max-w-sm mx-auto w-full px-4 py-8 mt-10 sm:mt-0">
                        {{ $slot }}
                    </div>

                </div>

            </div>

            <!-- Right Content -->
            <div class="hidden md:block md:w-1/2 overflow-hidden right-side-content" aria-hidden="true">
                <div class="flex items-center justify-center w-full h-full login-logo animate-slide-in-right-delay">
                    <img class="w-1/2 h-1/2 object-contain" src="{{ asset('images/beahoa.jpeg') }}" width="760"
                        height="1024" alt="BEAHOA Logo" />
                </div>
            </div>

        </div>

    </main>

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
