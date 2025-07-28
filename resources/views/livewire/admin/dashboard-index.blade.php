<div>
    <x-dashboard.welcome-banner />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <!-- Summary Cards -->
        <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Total Participants</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalSum) }}</h3>
                </div>
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Add more summary cards here if needed -->
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="space-y-1">
                <h2 class="text-xl font-semibold text-gray-800">{{ $advocacyInfo->name }} Support Levels</h2>
                <p class="text-sm text-gray-500">Breakdown by gender and age group</p>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <label for="dimension-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Dimension</label>
                    <select id="dimension-select" wire:model.live="selectedDimension"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-48">
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label for="year-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Year</label>
                    <select id="year-select" wire:model.live="selectedYear"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-24">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label for="age-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Age Group</label>
                    <select id="age-select" wire:model.live="selectedAge"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-28">
                        @foreach ($ageOptions as $age)
                            <option value="{{ $age }}">{{ $age }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Chart Canvas -->
        <div class="h-80 relative">
            <canvas id="advocacyChart" wire:ignore class="w-full h-full"></canvas>

            @if ($loading)
                <div class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg">
                    <div class="text-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Loading data...</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Gender Breakdown -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach (['Male', 'Female', 'Others'] as $index => $gender)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $gender }}</p>
                            <h3 class="text-xl font-semibold text-gray-800 mt-1">
                                {{ number_format($chartData[$index] ?? 0) }}
                            </h3>
                        </div>
                        <div
                            class="text-2xl font-bold @if ($index === 0) text-blue-600 @elseif($index === 1) text-green-600 @else text-red-600 @endif">
                            {{ $totalSum > 0 ? round(($chartData[$index] / $totalSum) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            @endforeach
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

                // Destroy existing chart if it exists
                if (chart) {
                    chart.destroy();
                }

                // Create gradients
                const colors = [
                    'rgba(59, 130, 246, 0.9)', // blue
                    'rgba(16, 185, 129, 0.9)', // green
                    'rgba(244, 63, 94, 0.9)', // red
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
                            label: 'Participants',
                            data: data,
                            backgroundColor: gradientColors,
                            borderColor: colors.map(c => c.replace('0.9', '1')),
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 800,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.9)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 14
                                },
                                callbacks: {
                                    label: (context) => {
                                        const total = context.dataset.data.reduce((a, b) => a + b,
                                            0);
                                        const value = context.parsed.y;
                                        const percentage = total > 0 ? Math.round((value / total) *
                                            100) : 0;
                                        return `${value} (${percentage}%)`;
                                    },
                                    title: (context) => `${context[0].label} Participants`
                                }
                            },
                            datalabels: {
                                display: false // We'll enable this if we want to show values on bars
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    color: 'rgba(0,0,0,0.6)',
                                    padding: 8,
                                    callback: (value) => Number(value) === value ? value
                                        .toLocaleString() : value
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: 'rgba(0,0,0,0.6)',
                                    padding: 8
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
        });
    </script>
@endpush
