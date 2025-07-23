<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-5">

            <h1 class="text-3xl font-bold text-gray-800">Our Advocacies ({{ $advocacyInfo->name ?? 'Advocacy' }})</h1>

            <a href="{{ url()->previous() }}"
                class="flex items-center justify-between text-blue-600 hover:text-blue-800 transition-colors">
                <i class="bi bi-skip-backward mr-2"></i>
                Back
            </a>
        </div>

        <!-- Intro Section -->
        <div class="w-full bg-white rounded-2xl p-3 sm:p-6 shadow mb-5">
            <!-- Description Section -->
            <p class="text-gray-700 text-lg leading-relaxed">
                {{ $advocacyInfo->description ?? 'No description available' }}
            </p>

            <!-- Impact Measurement Section -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 shadow-sm mt-4">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-blue-600 mr-3 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-800 mb-2 text-lg">How We Measure Impact</h3>
                        @if ($advocacyInfo->indicators && $advocacyInfo->indicators->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($advocacyInfo->indicators as $indicator)
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ $indicator->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-blue-700 italic">No indicators available</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-blue-600 font-bold text-2xl mb-1">
                        {{ $advocacyInfo->pydiDatasetDetals->count() ?? '15K+' }}
                    </div>
                    <div class="text-gray-600 text-sm">Survey Respondents</div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-blue-600 font-bold text-2xl mb-1">
                        {{ $advocacyInfo->pydiDatasetDetals->count() !== 0 ? '100%' : '0%' }}
                    </div>
                    <div class="text-gray-600 text-sm">Data Accuracy</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <h2 class="text-xl font-semibold text-gray-800">Advocacy Support Levels</h2>

                <!-- Dropdowns for Year & Age -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <select wire:model.live="selectedYear"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-24">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="selectedAge"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-28">
                        @foreach ($ageOptions as $age)
                            <option value="{{ $age }}">{{ $age }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="h-80 relative">
                <canvas id="advocacyChart" wire:ignore class="w-full h-full"></canvas>
                <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let chart;

            const initChart = (labels, data) => {
                const ctx = document.getElementById('advocacyChart').getContext('2d');

                // Create gradients
                const colors = [
                    'rgba(59, 130, 246, 0.9)',
                    'rgba(16, 185, 129, 0.9)',
                    'rgba(244, 63, 94, 0.9)',
                ];

                const gradientColors = colors.map(color => {
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, color.replace('0.9', '0.8'));
                    gradient.addColorStop(1, color.replace('0.9', '0.4'));
                    return gradient;
                });

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Support Level',
                            data: data,
                            backgroundColor: gradientColors,
                            borderColor: colors.map(c => c.replace('0.9', '1')),
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 600,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                callbacks: {
                                    label: (context) => `${context.parsed.y}`,
                                    title: (context) => `${context[0].label}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgba(0,0,0,0.6)',
                                    callback: (value) => value
                                }
                            },
                            x: {
                                ticks: {
                                    color: 'rgba(0,0,0,0.6)'
                                }
                            }
                        }
                    }
                });
            };

            // Initialize chart with initial data
            initChart(@js($chartLabels), @js($chartData));

            // Update chart when data changes
            Livewire.on('chart-updated', (event) => {
                if (chart) {
                    chart.data.datasets[0].data = event.data;
                    chart.update();
                }
            });

            // Reinitialize chart when Livewire component is updated
            Livewire.hook('element.updated', (el, component) => {
                if (chart && component.serverMemo.data.chartData) {
                    chart.data.datasets[0].data = component.serverMemo.data.chartData;
                    chart.update();
                }
            });
        });
    </script>
@endpush
