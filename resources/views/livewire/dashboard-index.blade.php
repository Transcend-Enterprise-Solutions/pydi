<div>
    <x-dashboard.welcome-banner />

    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="space-y-1">
                <h2 class="text-xl font-semibold text-gray-800">PYDI Support Levels</h2>
                <p class="text-sm text-gray-500">
                    Breakdown by gender and age group
                    @if($isPercentage)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            Percentage Values
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                            Frequency Values
                        </span>
                    @endif
                </p>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <label for="dimension-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Dimension</label>
                    <select id="dimension-select" wire:model.live="selectedDimension"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-48">
                        <option value="">All Dimensions</option>
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label for="indicator-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Indicator</label>
                    <select id="indicator-select" wire:model.live="selectedIndicator"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-72">
                        <option value="">All Indicators</option>
                        @foreach ($indicators as $indicator)
                            <option value="{{ $indicator->id }}">
                                {{ $indicator->name }}
                                <small class="text-gray-500">({{ ucfirst($indicator->measurement_unit) }})</small>
                            </option>
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
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach (['Male', 'Female', 'Others'] as $index => $gender)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $gender }}</p>
                            <h3 class="text-xl font-semibold text-gray-800 mt-1">
                                @if($isPercentage)
                                    {{ number_format($chartData[$index] ?? 0, 1) }}%
                                @else
                                    {{ number_format($chartData[$index] ?? 0) }}
                                @endif
                            </h3>
                        </div>
                        <div
                            class="text-2xl font-bold
                        @if ($index === 0) text-blue-600
                        @elseif($index === 1) text-green-600
                        @else text-red-600 @endif">
                            @if($isPercentage)
                                {{ $totalSum > 0 ? round(($chartData[$index] / $totalSum) * 100, 1) : 0 }}%
                            @else
                                {{ $totalSum > 0 ? round(($chartData[$index] / $totalSum) * 100, 1) : 0 }}%
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Total Card --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Total</p>
                        <h3 class="text-xl font-semibold text-gray-900 mt-1">
                            @if($isPercentage)
                                {{ number_format($totalSum, 1) }}%
                            @else
                                {{ number_format($totalSum) }}
                            @endif
                        </h3>
                    </div>
                    <div class="text-2xl font-bold text-purple-600">
                        @if($isPercentage)
                            Avg
                        @else
                            {{ $totalSum != 0 ? '100%' : '0%' }}
                        @endif
                    </div>
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
            let currentMeasurementUnit = 'frequency';
            let currentIsPercentage = false;

            const initChart = (labels, data, measurementUnit = 'frequency', isPercentage = false) => {
                const ctx = document.getElementById('advocacyChart').getContext('2d');
                currentMeasurementUnit = measurementUnit;
                currentIsPercentage = isPercentage;

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
                            label: isPercentage ? 'Percentage' : 'Participants',
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
                                        const value = context.parsed.y;
                                        if (isPercentage) {
                                            return `${value.toFixed(1)}%`;
                                        } else {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return `${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    },
                                    title: (context) => {
                                        const suffix = isPercentage ? 'Percentage' : 'Participants';
                                        return `${context[0].label} ${suffix}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: isPercentage ? 100 : undefined, // Set max to 100 for percentage
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    color: 'rgba(0,0,0,0.6)',
                                    padding: 8,
                                    callback: (value) => {
                                        if (isPercentage) {
                                            return value + '%';
                                        } else {
                                            return Number(value) === value ? value.toLocaleString() : value;
                                        }
                                    }
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
            initChart(@js($chartLabels), @js($chartData), @js($measurementUnit), @js($isPercentage));

            // Update chart when data changes
            Livewire.on('chart-updated', (event) => {
                const eventData = Array.isArray(event) ? event[0] : event;
                const data = eventData.data || eventData;
                const measurementUnit = eventData.measurementUnit || 'frequency';
                const isPercentage = eventData.isPercentage || false;

                if (chart) {
                    // If measurement unit changed, reinitialize chart
                    if (currentMeasurementUnit !== measurementUnit || currentIsPercentage !== isPercentage) {
                        initChart(@js($chartLabels), data, measurementUnit, isPercentage);
                    } else {
                        // Just update data
                        chart.data.datasets[0].data = data;
                        chart.data.datasets[0].label = isPercentage ? 'Percentage' : 'Participants';
                        chart.update();
                    }
                }
            });
        });
    </script>
@endpush
