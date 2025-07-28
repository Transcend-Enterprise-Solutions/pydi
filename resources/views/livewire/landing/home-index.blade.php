<div>
    <!-- Hero Section -->
    <section class="relative py-32 md:py-40 overflow-hidden">
        <!-- Background Image with Gradient Overlay -->
        <div class="absolute inset-0">
            <img src="{{ url('/images/banner.png') }}" alt="Empowered Filipino Youth"
                class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-blue-800/70"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <!-- Badge -->
                <span
                    class="inline-block px-4 py-2 mb-6 text-sm font-semibold text-blue-100 bg-blue-600/30 rounded-full backdrop-blur-sm border border-blue-400/20 animate-fadeIn">
                    National Youth Commission Initiative
                </span>

                <!-- Main Heading -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6 animate-fadeIn"
                    style="animation-delay: 0.2s">
                    Empowering the <span class="text-yellow-300">Youth</span> of the Philippines
                </h1>

                <!-- Subheading -->
                <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8 opacity-90 animate-fadeIn"
                    style="animation-delay: 0.4s">
                    Track, analyze, and improve youth development indicators across all regions with the Philippine
                    Youth Development Index.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fadeIn"
                    style="animation-delay: 0.6s">
                    <a href="{{ route('register') }}"
                        class="px-8 py-4 bg-white text-blue-800 font-bold rounded-lg shadow-lg hover:bg-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        Get Started Today
                        <i class="bi bi-arrow-right ml-2"></i>
                    </a>
                    <a href="#features"
                        class="px-8 py-4 bg-transparent text-white font-bold rounded-lg border-2 border-white hover:bg-white/10 transition-all duration-300">
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Scrolling Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#features" class="text-white">
                <i class="bi bi-chevron-down text-3xl"></i>
            </a>
        </div>

        <!-- Stats Bar (Optional) -->
        <div class="absolute bottom-0 left-0 right-0 bg-white/10 backdrop-blur-sm py-4">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-white">
                    <div class="p-2 animate-stagger" style="animation-delay: 0.1s">
                        <div class="text-2xl md:text-3xl font-bold">17</div>
                        <div class="text-sm opacity-80">Regions Covered</div>
                    </div>
                    <div class="p-2 animate-stagger" style="animation-delay: 0.2s">
                        <div class="text-2xl md:text-3xl font-bold">42M+</div>
                        <div class="text-sm opacity-80">Youth Tracked</div>
                    </div>
                    <div class="p-2 animate-stagger" style="animation-delay: 0.3s">
                        <div class="text-2xl md:text-3xl font-bold">50+</div>
                        <div class="text-sm opacity-80">Development Indicators</div>
                    </div>
                    <div class="p-2 animate-stagger" style="animation-delay: 0.4s">
                        <div class="text-2xl md:text-3xl font-bold">100+</div>
                        <div class="text-sm opacity-80">Partner Organizations</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Separator -->
    <div class="relative h-16 w-full overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-full bg-gradient-to-b from-blue-900/80 to-gray-50"></div>
        <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gray-50"></div>
    </div>

    <!-- Advocacies Section -->
    <section id="advocacies" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fadeInUp">
                <span
                    class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">Our
                    Focus Areas</span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Youth Development Advocacies</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Comprehensive initiatives addressing key dimensions
                    of youth empowerment and growth</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($dimensions as $index => $dimension)
                    @php
                        $totalProjects = $dimension->pydiDatasetDetals->count();
                        $rawSum = $dimension->pydiDatasetDetals->sum('content');

                        if ($rawSum < 1000) {
                            $totalSum = $rawSum . '+';
                        } elseif ($rawSum < 1000000) {
                            $totalSum = number_format($rawSum / 1000, 1) . 'K+';
                        } else {
                            $totalSum = number_format($rawSum / 1000000, 1) . 'M+';
                        }
                    @endphp

                    <div class="group bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 animate-fadeInUp"
                        style="animation-delay: {{ $index * 0.1 + 0.2 }}s">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $dimension['image'] }}" alt="{{ $dimension['name'] }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-2xl font-bold text-white">{{ $dimension['name'] }}</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="bi bi-people-fill mr-2"></i>
                                    {{ $totalSum }} Participants
                                </span>
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ $dimension['projects'] ?? $totalProjects }} Projects
                                </span>
                            </div>
                            <p class="text-gray-600 mb-4">{{ $dimension['description'] }}</p>
                            <a href="{{ route('advocacy', $dimension['id']) }}"
                                class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                                Learn more
                                <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- Section Separator -->
    <div class="relative h-16 w-full overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-full bg-gradient-to-b from-gray-50 to-white"></div>
        <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                opacity=".25" class="fill-current text-gray-100"></path>
            <path
                d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                opacity=".5" class="fill-current text-gray-100"></path>
            <path
                d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                class="fill-current text-white"></path>
        </svg>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fadeInUp">
                <span
                    class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">Why
                    Choose PYDI</span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features for Youth Development</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Comprehensive tools designed to help you understand
                    and improve youth outcomes nationwide</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 animate-fadeInUp"
                    style="animation-delay: 0.1s">
                    <div
                        class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-100 transition-colors">
                        <i class="bi bi-graph-up-arrow text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Data Insights</h3>
                    <p class="text-gray-600 text-center">Access real-time analytics and comprehensive reports on youth
                        development indicators across all regions.</p>
                    <div class="mt-6 text-center">
                        <a href="#"
                            class="inline-flex items-center text-blue-600 font-medium group-hover:text-blue-800 transition-colors">
                            Explore data
                            <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="group bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 animate-fadeInUp"
                    style="animation-delay: 0.2s">
                    <div
                        class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-100 transition-colors">
                        <i class="bi bi-people-fill text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Community Focus</h3>
                    <p class="text-gray-600 text-center">Connect with 100+ organizations driving youth programs and
                        collaborate on impactful initiatives.</p>
                    <div class="mt-6 text-center">
                        <a href="#"
                            class="inline-flex items-center text-blue-600 font-medium group-hover:text-blue-800 transition-colors">
                            Join community
                            <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="group bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 animate-fadeInUp"
                    style="animation-delay: 0.3s">
                    <div
                        class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 mx-auto group-hover:bg-blue-100 transition-colors">
                        <i class="bi bi-globe2 text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Nationwide Coverage</h3>
                    <p class="text-gray-600 text-center">Monitor and compare youth development initiatives across all
                        17 regions of the Philippines.</p>
                    <div class="mt-6 text-center">
                        <a href="#"
                            class="inline-flex items-center text-blue-600 font-medium group-hover:text-blue-800 transition-colors">
                            View regions
                            <i class="bi bi-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Separator -->
    <div class="relative h-16 w-full overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-full bg-gradient-to-b from-gray-50 to-white"></div>
        <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-white"></div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex items-center gap-12">
                <div class="md:w-1/2 mb-10 md:mb-0 animate-fadeInLeft">
                    <div class="relative rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ url('/images/banner1.png') }}" alt="PYDI Team" class="w-full h-auto">
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/30 to-transparent"></div>
                    </div>
                </div>
                <div class="md:w-1/2 animate-fadeInRight">
                    <span
                        class="inline-block px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-4">Our
                        Mission</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">About PYDI</h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        The Philippine Youth Development Index (PYDI) is a comprehensive platform empowering government
                        agencies, NGOs, and communities to track and improve youth development through data-driven
                        insights and collaborative tools.
                    </p>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xl"></i>
                            </div>
                            <p class="ml-3 text-gray-600">
                                <span class="font-semibold">17 Regions Covered:</span> Nationwide monitoring of youth
                                development indicators
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xl"></i>
                            </div>
                            <p class="ml-3 text-gray-600">
                                <span class="font-semibold">50+ Metrics:</span> Comprehensive tracking across multiple
                                dimensions of youth development
                            </p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xl"></i>
                            </div>
                            <p class="ml-3 text-gray-600">
                                <span class="font-semibold">Collaborative Platform:</span> Connect with 100+ partner
                                organizations
                            </p>
                        </div>
                    </div>
                    <a href="#"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        Learn more about our methodology
                        <i class="bi bi-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes stagger {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease-out forwards;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 0.8s ease-out forwards;
        }

        .animate-fadeInRight {
            animation: fadeInRight 0.8s ease-out forwards;
        }

        .animate-stagger {
            animation: stagger 0.6s ease-out forwards;
        }
    </style>
</div>
