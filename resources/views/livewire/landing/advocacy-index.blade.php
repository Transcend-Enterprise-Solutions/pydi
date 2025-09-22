<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-5">
            <h1 class="text-3xl font-bold text-gray-800">Our Advocacies ({{ $advocacyInfo->name ?? 'Advocacy' }})</h1>

            <div class="flex gap-2 items-center">
                <select wire:model.live="selectedAdvocacy"
                    class="w-42 py-2 px-3 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    @foreach ($demensions as $row)
                        <option value="{{ $row['id'] }}">{{ $row['name'] }}</option>
                    @endforeach
                </select>

                <a href="{{ url('/') }}"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-sm flex items-center gap-2">
                    <i class="bi bi-skip-backward mr-1"></i>
                    <span>Back</span>
                </a>
            </div>
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
                                        <span>{{ $indicator->name }} ({{ ucfirst($indicator->measurement_unit) }})</span>
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
                        @if($isPercentage)
                            {{ number_format($totalSum, 1) }}%
                        @else
                            {{ number_format($totalSum) }}
                        @endif
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ $isPercentage ? 'Average Support Level' : 'Total Respondents' }}
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="text-blue-600 font-bold text-2xl mb-1">
                        {{ $advocacyInfo->pydiDatasetDetails->count() !== 0 ? '100%' : '0%' }}
                    </div>
                    <div class="text-gray-600 text-sm">Data Accuracy</div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-semibold text-gray-800">Advocacy Support Levels</h2>
                    <p class="text-sm text-gray-500">
                        Breakdown by gender and demographic filters
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
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <!-- Indicator Filter -->
                <div class="relative">
                    <label for="indicator-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Indicator</label>
                    <select id="indicator-select" wire:model.live="selectedIndicator"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-48">
                        <option value="">All Indicators</option>
                        @foreach ($indicators as $indicator)
                            <option value="{{ $indicator['id'] }}">
                                {{ $indicator['name'] }}
                                <small class="text-gray-500">({{ ucfirst($indicator['measurement_unit']) }})</small>
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="relative">
                    <label for="year-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Year</label>
                    <select id="year-select" wire:model.live="selectedYear"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-24">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Age Group Filter -->
                <div class="relative">
                    <label for="age-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Age Group</label>
                    <select id="age-select" wire:model.live="selectedAge"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-32">
                        @foreach ($ageOptions as $age)
                            <option value="{{ $age }}">{{ $age }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sex Filter -->
                <div class="relative">
                    <label for="sex-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Sex</label>
                    <select id="sex-select" wire:model.live="selectedSex"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-32">
                        @foreach ($sexOptions as $sex)
                            <option value="{{ $sex }}">{{ $sex }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Region Filter -->
                <div class="relative">
                    <label for="region-select"
                        class="absolute -top-2 left-2 px-1 text-xs font-medium text-gray-500 bg-white">Region</label>
                    <select id="region-select" wire:model.live="selectedRegion"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full min-w-48">
                        <option value="">All Regions</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region['id'] }}">{{ $region['region_description'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <div class="relative flex items-end">
                    <button wire:click="$set('selectedAge', 'All Ages'); $set('selectedSex', 'All Sexes'); $set('selectedRegion', ''); $set('selectedIndicator', '');"
                        class="px-4 py-2 text-sm bg-gray-100 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-200 focus:ring-blue-500 focus:border-blue-500 w-full transition-colors">
                        Clear Filters
                    </button>
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

            <!-- Gender Breakdown Cards -->
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

                <!-- Total Card -->
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

            <!-- Filter Summary -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Active Filters:</h3>
                <div class="flex flex-wrap gap-2">
                    @if($selectedIndicator && collect($indicators)->where('id', $selectedIndicator)->first())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            Indicator: {{ collect($indicators)->where('id', $selectedIndicator)->first()['name'] ?? 'Unknown' }}
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
                    @if($selectedAge === 'All Ages' && $selectedSex === 'All Sexes' && !$selectedRegion && !$selectedIndicator)
                        <span class="text-xs text-gray-500">No specific filters applied (showing all data)</span>
                    @endif
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
                            label: isPercentage ? 'Percentage' : 'Support Level',
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
                                        const suffix = isPercentage ? 'Percentage' : 'Support Level';
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
                        chart.data.datasets[0].label = isPercentage ? 'Percentage' : 'Support Level';
                        chart.update();
                    }
                }
            });

            // Reinitialize chart when Livewire component is updated
            Livewire.hook('element.updated', (el, component) => {
                if (chart && component.serverMemo.data.chartData) {
                    const isPercentage = component.serverMemo.data.isPercentage || false;
                    const measurementUnit = component.serverMemo.data.measurementUnit || 'frequency';

                    // If measurement unit changed, reinitialize chart
                    if (currentMeasurementUnit !== measurementUnit || currentIsPercentage !== isPercentage) {
                        initChart(@js($chartLabels), component.serverMemo.data.chartData, measurementUnit, isPercentage);
                    } else {
                        chart.data.datasets[0].data = component.serverMemo.data.chartData;
                        chart.update();
                    }
                }
            });
        });
    </script>
@endpush
