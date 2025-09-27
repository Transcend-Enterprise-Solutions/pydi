<div>
    <x-dashboard.welcome-banner />

    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col mb-6 gap-4">
            <div class="space-y-1">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">PYDI Support Levels</h2>
                <p class="text-sm text-gray-600 dark:text-gray-200">
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
            <div class="grid grid-cols-3 gap-4 w-full">
                <!-- Dimension Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="dimension-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Dimension</label>
                    <select id="dimension-select" wire:model.live="selectedDimension"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-48 dark:bg-slate-800 dark:border-gray-700">
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension['id'] }}">{{ $dimension['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Indicator Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="indicator-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Indicator</label>
                    <select id="indicator-select" wire:model.live="selectedIndicator"
                        class="dark:bg-slate-800 dark:border-gray-700 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-72">
                        @foreach ($indicators as $indicator)
                            <option value="{{ $indicator['id'] }}">
                                {{ $indicator['name'] }}
                                @if(isset($indicator['measurement_unit']) && $indicator['measurement_unit'] === 'percentage')
                                    <small class="text-gray-500">(Percentage)</small>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="year-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Year</label>
                    <select id="year-select" wire:model.live="selectedYear"
                        class="dark:bg-slate-800 dark:border-gray-700 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-24">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Age Group Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="age-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Age Group</label>
                    <select id="age-select" wire:model.live="selectedAge"
                        class="dark:bg-slate-800 dark:border-gray-700 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-32">
                        @foreach ($ageOptions as $age)
                            <option value="{{ $age }}">{{ $age }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sex Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="sex-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Sex</label>
                    <select id="sex-select" wire:model.live="selectedSex"
                        class="dark:bg-slate-800 dark:border-gray-700 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-32">
                        @foreach ($sexOptions as $sex)
                            <option value="{{ $sex }}">{{ $sex }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Region Filter -->
                <div class="relative col-span-full md:col-span-1 lg:col-span-1">
                    <label for="region-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-gray-800">Region</label>
                    <select id="region-select" wire:model.live="selectedRegion"
                        class="dark:bg-slate-800 dark:border-gray-700 px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-48">
                        <option value="">All Regions</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region['id'] }}">{{ $region['region_description'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Summary -->
            <div class="mt-2">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">Active Filters:</h3>
                <div class="flex flex-wrap gap-2">
                    @if($selectedDimension && collect($dimensions)->where('id', $selectedDimension)->first())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            Dimension: {{ collect($dimensions)->where('id', $selectedDimension)->first()['name'] ?? 'Unknown' }}
                        </span>
                    @endif
                    @if($selectedAge !== 'All Ages')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Age: {{ $selectedAge }}
                        </span>
                    @endif
                    @if($selectedSex !== 'All Sexes')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Sex: {{ $selectedSex }}
                        </span>
                    @endif
                    @if($selectedRegion && collect($regions)->where('id', $selectedRegion)->first())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Region: {{ collect($regions)->where('id', $selectedRegion)->first()['region_description'] ?? 'Unknown' }}
                        </span>
                    @endif
                    @if($selectedYear)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Year: {{ $selectedYear }}
                        </span>
                    @endif
                    @if(!$selectedAge === 'All Ages' && !$selectedSex === 'All Sexes' && !$selectedRegion && !$selectedYear && !$selectedDimension)
                        <span class="text-xs text-gray-500">No filters applied</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chart Canvas -->
        <div class="h-80 relative">
            <div class="block dark:hidden" style="height: 340px">
                <canvas id="advocacyChart" wire:ignore class="w-full h-full"></canvas>
            </div>
            <div class="hidden dark:block" style="height: 340px">
                <canvas id="advocacyChartDark" wire:ignore class="w-full h-full"></canvas>
            </div>

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
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach (['Male', 'Female', 'Others'] as $index => $gender)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 dark:bg-slate-800 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">{{ $gender }}</p>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-1">
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
                            @if($totalSum > 0)
                                {{ round(($chartData[$index] / $totalSum) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Total Card --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 dark:bg-slate-800 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total</p>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-1">
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

{{-- @push('scripts')
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
                            label: isPercentage ? 'Percentage' : 'Count',
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
                                        return context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: isPercentage ? 100 : undefined,
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
                        chart.data.datasets[0].label = isPercentage ? 'Percentage' : 'Count';
                        chart.update();
                    }
                }
            });
        });
    </script>
@endpush --}}

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let chart;
            let chartDark;
            let currentMeasurementUnit = 'frequency';
            let currentIsPercentage = false;

            const initChart = (labels, data, measurementUnit = 'frequency', isPercentage = false) => {
                currentMeasurementUnit = measurementUnit;
                currentIsPercentage = isPercentage;

                // Initialize light mode chart
                initLightChart(labels, data, measurementUnit, isPercentage);
                
                // Initialize dark mode chart
                initDarkChart(labels, data, measurementUnit, isPercentage);
            };

            const initLightChart = (labels, data, measurementUnit, isPercentage) => {
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
                            label: isPercentage ? 'Percentage' : 'Count',
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
                                        return context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: isPercentage ? 100 : undefined,
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

            const initDarkChart = (labels, data, measurementUnit, isPercentage) => {
                const ctx = document.getElementById('advocacyChartDark').getContext('2d');

                // Destroy existing chart if it exists
                if (chartDark) {
                    chartDark.destroy();
                }

                // Create gradients for dark mode
                const darkColors = [
                    'rgba(96, 165, 250, 0.9)', // lighter blue
                    'rgba(52, 211, 153, 0.9)', // lighter green
                    'rgba(248, 113, 113, 0.9)', // lighter red
                ];

                const gradientColors = darkColors.map(color => {
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, color.replace('0.9', '0.8'));
                    gradient.addColorStop(1, color.replace('0.9', '0.4'));
                    return gradient;
                });

                chartDark = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: isPercentage ? 'Percentage' : 'Count',
                            data: data,
                            backgroundColor: gradientColors,
                            borderColor: darkColors.map(c => c.replace('0.9', '1')),
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
                                backgroundColor: 'rgba(55, 65, 81, 0.95)',
                                titleColor: 'rgba(255, 255, 255, 0.9)',
                                bodyColor: 'rgba(255, 255, 255, 0.9)',
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
                                        return context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: isPercentage ? 100 : undefined,
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(255,255,255,0.1)'
                                },
                                ticks: {
                                    color: 'rgba(255,255,255,0.7)',
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
                                    color: 'rgba(255,255,255,0.7)',
                                    padding: 8
                                }
                            }
                        }
                    }
                });
            };

            // Initialize charts with initial data
            initChart(@js($chartLabels), @js($chartData), @js($measurementUnit), @js($isPercentage));

            // Update charts when data changes
            Livewire.on('chart-updated', (event) => {
                const eventData = Array.isArray(event) ? event[0] : event;
                const data = eventData.data || eventData;
                const measurementUnit = eventData.measurementUnit || 'frequency';
                const isPercentage = eventData.isPercentage || false;

                // If measurement unit changed, reinitialize charts
                if (currentMeasurementUnit !== measurementUnit || currentIsPercentage !== isPercentage) {
                    initChart(@js($chartLabels), data, measurementUnit, isPercentage);
                } else {
                    // Just update data for both charts
                    if (chart) {
                        chart.data.datasets[0].data = data;
                        chart.data.datasets[0].label = isPercentage ? 'Percentage' : 'Count';
                        chart.update();
                    }
                    
                    if (chartDark) {
                        chartDark.data.datasets[0].data = data;
                        chartDark.data.datasets[0].label = isPercentage ? 'Percentage' : 'Count';
                        chartDark.update();
                    }
                }
            });
        });
    </script>
@endpush
