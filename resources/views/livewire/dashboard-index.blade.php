<div>
    <x-dashboard.welcome-banner />

    <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="space-y-1">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">PYDI Support Levels</h2>
                <p class="text-sm">
                    Breakdown by gender and age group
                    @if($isPercentage)
                        <span class="inline-flex items-center px-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            Percentage Values
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                            Frequency Values
                        </span>
                    @endif
                </p>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <label for="dimension-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium bg-white dark:bg-slate-800">Dimension</label>
                    <select id="dimension-select" wire:model.live="selectedDimension"
                        class="px-4 py-2 text-sm border dark:bg-slate-800 dark:border-gray-700 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-48">
                        <option value="">All Dimensions</option>
                        @foreach ($dimensions as $dimension)
                            <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label for="indicator-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium dark:bg-slate-800 bg-white">Indicator</label>
                    <select id="indicator-select" wire:model.live="selectedIndicator"
                        class="px-4 py-2 text-sm border dark:bg-slate-800 dark:border-gray-700 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-72">
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
                        class="absolute -top-2 left-2 px-1 text-xs font-medium dark:bg-slate-800 bg-white">Year</label>
                    <select id="year-select" wire:model.live="selectedYear"
                        class="px-4 py-2 text-sm border dark:bg-slate-800 dark:border-gray-700 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-24">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative">
                    <label for="age-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium dark:bg-slate-800 bg-white">Age Group</label>
                    <select id="age-select" wire:model.live="selectedAge"
                        class="px-4 py-2 text-sm border dark:bg-slate-800 dark:border-gray-700 border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-28">
                        @foreach ($ageOptions as $age)
                            <option value="{{ $age }}">{{ $age }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Chart Canvas -->
        <div class="h-80 relative">
            <div class="w-full h-full block dark:hidden">
                <canvas id="advocacyChart" wire:ignore class="w-full h-full"></canvas>
            </div>
            <div class="w-full h-full hidden dark:block">
                <canvas id="advocacyChart2" wire:ignore class="w-full h-full"></canvas>
            </div>

            @if ($loading)
                <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-700 bg-opacity-80 rounded-lg">
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
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-200">{{ $gender }}</p>
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
            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Total</p>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-1">
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            let chart;
            let currentMeasurementUnit = 'frequency';
            let currentIsPercentage = false;

            // Function to detect dark mode
            const isDarkMode = () => {
                return document.documentElement.classList.contains('dark') || 
                       (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            };

            // Function to get theme-appropriate colors
            const getThemeColors = () => {
                const darkMode = isDarkMode();
                
                return {
                    // Chart colors
                    colors: [
                        darkMode ? 'rgba(96, 165, 250, 0.9)' : 'rgba(59, 130, 246, 0.9)', // blue
                        darkMode ? 'rgba(52, 211, 153, 0.9)' : 'rgba(16, 185, 129, 0.9)', // green
                        darkMode ? 'rgba(251, 113, 133, 0.9)' : 'rgba(244, 63, 94, 0.9)', // red
                    ],
                    // Grid colors
                    gridColor: darkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)',
                    // Text colors
                    textColor: darkMode ? 'rgba(255, 255, 255, 0.8)' : 'rgba(0, 0, 0, 0.6)',
                    // Tooltip background
                    tooltipBg: darkMode ? 'rgba(31, 41, 55, 0.95)' : 'rgba(0, 0, 0, 0.9)'
                };
            };

            const initChart = (labels, data, measurementUnit = 'frequency', isPercentage = false) => {
                const ctx = document.getElementById('advocacyChart2').getContext('2d');
                currentMeasurementUnit = measurementUnit;
                currentIsPercentage = isPercentage;

                // Destroy existing chart if it exists
                if (chart) {
                    chart.destroy();
                }

                const themeColors = getThemeColors();

                // Create gradients with theme colors
                const gradientColors = themeColors.colors.map(color => {
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
                            borderColor: themeColors.colors.map(c => c.replace('0.9', '1')),
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
                                backgroundColor: themeColors.tooltipBg,
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
                                        const suffix = isPercentage ? 'Percentage' : 'Participants';
                                        return `${context[0].label} ${suffix}`;
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
                                    color: themeColors.gridColor
                                },
                                ticks: {
                                    color: themeColors.textColor,
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
                                    color: themeColors.textColor,
                                    padding: 8
                                }
                            }
                        }
                    }
                });
            };

            // Function to update chart theme
            const updateChartTheme = () => {
                if (chart) {
                    const themeColors = getThemeColors();
                    const ctx = chart.ctx;
                    
                    // Update gradients
                    const gradientColors = themeColors.colors.map(color => {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, color.replace('0.9', '0.8'));
                        gradient.addColorStop(1, color.replace('0.9', '0.4'));
                        return gradient;
                    });
                    
                    // Update chart colors
                    chart.data.datasets[0].backgroundColor = gradientColors;
                    chart.data.datasets[0].borderColor = themeColors.colors.map(c => c.replace('0.9', '1'));
                    
                    // Update scales colors
                    chart.options.scales.y.grid.color = themeColors.gridColor;
                    chart.options.scales.y.ticks.color = themeColors.textColor;
                    chart.options.scales.x.ticks.color = themeColors.textColor;
                    
                    // Update tooltip colors
                    chart.options.plugins.tooltip.backgroundColor = themeColors.tooltipBg;
                    
                    chart.update();
                }
            };

            // Initialize chart with initial data
            initChart(@js($chartLabels), @js($chartData), @js($measurementUnit), @js($isPercentage));

            // Listen for theme changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && 
                        mutation.attributeName === 'class' && 
                        mutation.target === document.documentElement) {
                        updateChartTheme();
                    }
                });
            });

            // Start observing
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Also listen for system theme changes
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateChartTheme);
            }

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

            // Clean up observers when page unloads
            window.addEventListener('beforeunload', () => {
                observer.disconnect();
            });
        });
    </script>
@endpush
