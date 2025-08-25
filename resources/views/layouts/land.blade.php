<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ open: false }" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/nyc-logo_orig.png" type="image/x-icon">

    <title>{{ $title ?? 'NYC PYDI' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="build/assets/app-BUdMCiQf.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="build/assets/app-m1OUMg2T.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <!-- Navbar -->
    <nav x-data="{ mobileOpen: false }" class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 flex-shrink-0">
                    <img src="/images/nyc-logo_orig.png" alt="NYC Logo" class="h-8 w-8">
                    <span class="text-xl font-bold text-blue-600">NYC - PYDI</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <!-- Main Links -->

                    @php
                        $homeActive = request()->is('advocacy*');
                    @endphp
                    <div class="flex items-center gap-4">
                        @if ($homeActive)
                            <a href="{{ url('/landing') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 relative group px-2 py-1 flex items-center gap-2">
                                <i class="bi bi-house-door text-lg"></i>
                                Home
                                <span
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-200 group-hover:w-4/5"></span>
                            </a>
                        @else
                            <a href="#advocacies"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 relative group px-2 py-1 flex items-center gap-2">
                                <i class="bi bi-megaphone text-lg"></i>
                                Advocacies
                                <span
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-200 group-hover:w-4/5"></span>
                            </a>
                            <a href="#features"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 relative group px-2 py-1 flex items-center gap-2">
                                <i class="bi bi-stars text-lg"></i>
                                Features
                                <span
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-200 group-hover:w-4/5"></span>
                            </a>
                            <a href="#about"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 relative group px-2 py-1 flex items-center gap-2">
                                <i class="bi bi-info-circle text-lg"></i>
                                About
                                <span
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-200 group-hover:w-4/5"></span>
                            </a>
                        @endif

                    </div>

                    <!-- Auth Links -->
                    <div class="flex items-center gap-4 pl-6 border-l border-gray-200 h-10">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2 text-gray-700 hover:text-blue-600 transition-colors px-3 py-1 rounded-lg hover:bg-blue-50">
                                <i class="bi bi-speedometer2"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium transition-colors px-3 py-1 rounded-lg hover:bg-blue-50">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm flex items-center gap-2">
                                <i class="bi bi-person-plus"></i>
                                <span>Register</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-md hover:bg-gray-100 focus:outline-none">
                    <i class="bi text-2xl" :class="mobileOpen ? 'bi-x-lg' : 'bi-list'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="md:hidden bg-white border-t shadow-lg">
            <div class="px-2 py-3 space-y-1">
                <a href="#features"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    Features
                </a>
                <a href="#advocacies"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    Advocacies
                </a>
                <a href="#about"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    About
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="bi bi-speedometer2 mr-2"></i>Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                            <i class="bi bi-box-arrow-right mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                        <i class="bi bi-box-arrow-in-right mr-2"></i>Sign In
                    </a>
                    <a href="{{ route('register') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="bi bi-person-plus mr-2"></i>Register
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    <!-- Main Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-16" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">PYDI Footer</h2>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
            <div class="space-y-4">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 flex-shrink-0">
                        <img src="/images/nyc-logo_orig.png" alt="NYC Logo" class="h-12 w-12">
                        <span class="text-2xl font-bold text-white-600">NYC - PYDI</span>
                    </a>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Philippine Youth Development Index tracking platform for a better future.
                </p>
                <div class="flex space-x-4 pt-2">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="/"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Home</a></li>
                    <li><a href="#features"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Features</a>
                    </li>
                    <li><a href="#indicators"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Indicators</a>
                    </li>
                    <li><a href="#about"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">About Us</a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Resources</h4>
                <ul class="space-y-3">
                    <li><a href="#"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Documentation</a>
                    </li>
                    <li><a href="#"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">API
                            Access</a></li>
                    <li><a href="#"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Research
                            Papers</a></li>
                    <li><a href="#"
                            class="text-gray-400 hover:text-white text-sm transition-colors duration-200">Methodology</a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Contact</h4>
                <address class="not-italic text-gray-400 text-sm space-y-3">
                    <div>
                        <span class="block font-medium text-white">Email:</span>
                        <a href="mailto:info@pydi.gov.ph"
                            class="hover:text-white transition-colors duration-200">info@pydi.gov.ph</a>
                    </div>
                    <div>
                        <span class="block font-medium text-white">Phone:</span>
                        <a href="tel:+63212345678" class="hover:text-white transition-colors duration-200">(02)
                            1234-5678</a>
                    </div>
                    <div>
                        <span class="block font-medium text-white">Address:</span>
                        <span>123 Youth Drive, Quezon City, Philippines</span>
                    </div>
                </address>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 pb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} Philippine Youth Development Index. All rights reserved. Developed By: <a href="https://transcend-enterprise.com/" target="_blank" class="hover:underline">Transcend Enterprise Solutions</a>.
                </p>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-400 text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-400 text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-gray-400 text-sm">Accessibility</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    @livewireScripts
</body>

</html>
