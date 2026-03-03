<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension, PydiDataset, Indicator, PhilippineRegion, PhilippineRegions, PydiDatasetDetail};

#[Layout('layouts.app')]
#[Title('Dashboard')]
class DashboardIndex extends Component
{
    public $advocacyInfo;
    public $yearOptions = [];
    public $indicators = [];
    public $dimensions = [];
    public $regions = [];
    public $ageOptions = ['15-17', '18-24', '25-30 ', 'All Ages'];
    public $sexOptions = ['Male', 'Female', 'Others', 'All Sexes'];

    // Type annotations for better IDE support
    /** @var \Illuminate\Database\Eloquent\Collection $dimensions */
    /** @var \Illuminate\Database\Eloquent\Collection $regions */
    /** @var \Illuminate\Database\Eloquent\Collection $indicators */

    public $selectedAge = 'All Ages';
    public $selectedSex = 'All Sexes';
    public $selectedRegion = '';
    public $selectedYear = '';
    public $selectedDimension = ""; // can be empty = all
    public $selectedIndicator = "";
    public $selectedAdvocacy = '';

    public $chartLabels = ['Male', 'Female', 'Others'];
    public $chartData = [0, 0, 0];
    public $totalSum = 0;
    public $loading = false;

    // New properties for measurement unit handling
    public $measurementUnit = 'frequency'; // default
    public $isPercentage = false;

    public function mount()
    {
        $this->dimensions = Dimension::orderBy('name')->get()->toArray();
        
        // Get regions and sort them in the desired order
        $regions = PhilippineRegions::select('id', 'region_description')->get();
        
        // Define the display order
        $regionOrder = [
            'National Capital Region (NCR)',
            'Cordillera Administrative Region (CAR)',
            'Region I - Ilocos Region',
            'Region II - Cagayan Valley',
            'Region III - Central Luzon',
            'Region IV-A - CALABARZON',
            'MIMAROPA Region',
            'Region V - Bicol Region',
            'Region VI - Western Visayas',
            'Region VII - Central Visayas',
            'Region VIII - Eastern Visayas',
            'Region IX - Zamboanga Peninsula',
            'Region X - Northern Mindanao',
            'Region XI - Davao Region',
            'Region XII - SOCCSKSARGEN',
            'Region XIII - Caraga',
            'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)',
            'Negros Island Region (NIR)',
        ];
        
        // Sort regions based on the defined order
        $this->regions = $regions->sortBy(function($region) use ($regionOrder) {
            $position = array_search($region->region_description, $regionOrder);
            return $position !== false ? $position : 999;
        })->values()->toArray();

        // Select the first dimension by default if available
        if (!empty($this->dimensions)) {
            $this->selectedDimension = $this->dimensions[0]['id'];

            // Load indicators for the first dimension
            $this->loadIndicatorsForDimension($this->selectedDimension);

            // Select the first indicator if available
            if (!empty($this->indicators)) {
                $firstIndicator = $this->indicators[0];
                $this->selectedIndicator = $firstIndicator['id'] ?? '';
                $this->measurementUnit = $firstIndicator['measurement_unit'] ?? 'frequency';
                $this->isPercentage = $this->measurementUnit === 'percentage';
            }
        } else {
            $this->selectedDimension = "";
            $this->indicators = [];
        }

        $this->yearOptions = PydiDataset::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $this->selectedYear = in_array(date('Y'), $this->yearOptions) ? date('Y') : ($this->yearOptions[0] ?? null);

        // Set default region to "All Regions" (empty value)
        $this->selectedRegion = '';

        $this->updateChartData();
    }

    public function updatedSelectedYear()
    {
        $this->updateChartData();
    }

    public function updatedSelectedAge()
    {
        $this->updateChartData();
    }

    public function updatedSelectedSex()
    {
        $this->updateChartData();
    }

    public function updatedSelectedRegion()
    {
        $this->updateChartData();
    }

    public function updatedSelectedDimension($value)
    {
        $this->loading = true;

        if (empty($value)) {
            $this->advocacyInfo = null; // All dimensions
            $this->indicators = [];
            $this->measurementUnit = 'frequency'; // Reset to default
            $this->isPercentage = false;
            $this->selectedIndicator = ""; // Reset indicator selection
        } else {
            $this->loadIndicatorsForDimension($value);
        }

        $this->updateChartData();
        $this->loading = false;
    }

    public function updatedSelectedIndicator($value)
    {
        $this->loading = true;

        // Update measurement unit based on selected indicator
        if (!empty($value)) {
            $indicator = Indicator::find($value);
            if ($indicator) {
                $this->measurementUnit = $indicator->measurement_unit ?? 'frequency';
                $this->isPercentage = $this->measurementUnit === 'percentage';
            }
        } else {
            // If no specific indicator selected, use dimension's first indicator or default
            if (!empty($this->selectedDimension) && !empty($this->indicators)) {
                $firstIndicator = $this->indicators[0];
                $this->measurementUnit = $firstIndicator['measurement_unit'] ?? 'frequency';
                $this->isPercentage = $this->measurementUnit === 'percentage';
            } else {
                $this->measurementUnit = 'frequency';
                $this->isPercentage = false;
            }
        }

        $this->updateChartData();
        $this->loading = false;
    }

    protected function loadIndicatorsForDimension($dimensionId)
    {
        $this->advocacyInfo = Dimension::with('indicators')->findOrFail($dimensionId);

        // Check if this dimension is "others"
        $dimension = Dimension::find($dimensionId);
        $isOthersDimension = $dimension && strtolower($dimension->name) === 'others';

        if ($isOthersDimension) {
            // For "others" dimension, get unique indicator_others_text values from existing data
            $this->indicators = $this->getCustomIndicatorsForOthersDimension();
        } else {
            // For regular dimensions, use the predefined indicators
            $this->indicators = $this->advocacyInfo->indicators->toArray();
        }

        // Set measurement unit based on first indicator if available
        if (!empty($this->indicators)) {
            $firstIndicator = $this->indicators[0];
            $this->measurementUnit = $firstIndicator['measurement_unit'] ?? 'frequency';
            $this->isPercentage = $this->measurementUnit === 'percentage';

            // Auto-select the first indicator
            $this->selectedIndicator = $firstIndicator['id'];
        } else {
            $this->selectedIndicator = ""; // No indicators available
            $this->measurementUnit = 'frequency';
            $this->isPercentage = false;
        }
    }

    protected function getCustomIndicatorsForOthersDimension()
    {
        // Get unique indicator_others_text values from PydiDatasetDetail for the "others" dimension
        $customIndicators = PydiDatasetDetail::whereHas('dimension', function($query) {
                $query->where('name', 'like', '%others%');
            })
            ->whereNotNull('indicator_others_text')
            ->where('indicator_others_text', '!=', '')
            ->distinct()
            ->pluck('indicator_others_text')
            ->map(function($indicatorText, $index) {
                return [
                    'id' => 'custom_' . ($index + 1), // Generate unique ID for custom indicators
                    'name' => $indicatorText,
                    'measurement_unit' => 'frequency' // Default measurement unit for custom indicators
                ];
            })
            ->toArray();

        return $customIndicators;
    }

    protected function updateChartData()
    {
        $this->loading = true;

        // Check if we're dealing with "others" dimension and custom indicator
        $isOthersDimension = false;
        $customIndicatorText = null;

        if (!empty($this->selectedDimension)) {
            $dimension = Dimension::find($this->selectedDimension);
            $isOthersDimension = $dimension && strtolower($dimension->name) === 'others';

            // Check if selected indicator is a custom indicator (starts with "custom_")
            if ($isOthersDimension && !empty($this->selectedIndicator) && strpos($this->selectedIndicator, 'custom_') === 0) {
                // Find the custom indicator text from the indicators array
                $selectedIndicatorData = collect($this->indicators)->firstWhere('id', $this->selectedIndicator);
                $customIndicatorText = $selectedIndicatorData['name'] ?? null;
            }
        }

        // Base query: fetch dataset details directly
        $query = PydiDatasetDetail::with(['dimension', 'indicator', 'pydiDataset']);

        // Dimension filter
        if (!empty($this->selectedDimension)) {
            $query->where('dimension_id', $this->selectedDimension);
        }

        // For "others" dimension with custom indicator, filter by indicator_others_text
        if ($isOthersDimension && $customIndicatorText) {
            $query->where('indicator_others_text', $customIndicatorText);
        } else if (!empty($this->selectedIndicator)) {
            // For regular dimensions, use indicator_id
            $query->where('indicator_id', $this->selectedIndicator);
        }

        // Age filter
        if ($this->selectedAge !== 'All Ages') {
            if (str_contains($this->selectedAge, '+')) {
                $ageMin = rtrim($this->selectedAge, '+');
                $query->where('age', '>=', $ageMin);
            } else {
                [$min, $max] = explode('-', $this->selectedAge);
                $query->whereBetween('age', [(int)$min, (int)$max]);
            }
        }

        // Sex filter
        if ($this->selectedSex !== 'All Sexes') {
            $query->where('sex', strtolower($this->selectedSex));
        }

        // Region filter
        if (!empty($this->selectedRegion)) {
            $query->where('philippine_region_id', $this->selectedRegion);
        }

        // User role filter
        if (auth()->user()->user_role === 'user') {
            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('user_id', auth()->id());
            });
        }

        // Year and status filter
        $query->whereHas('pydiDataset', function ($subQuery) {
            $subQuery->where('year', $this->selectedYear)
                    ->where('status', 'approved');
        });

        $datasetDetails = $query->get();

        // Initialize totals
        $totals = ['Male' => 0, 'Female' => 0, 'Others' => 0];
        $counts = ['Male' => 0, 'Female' => 0, 'Others' => 0];
        $this->totalSum = 0;

        // Sum values for all selected details
        foreach ($datasetDetails as $detail) {
            $sex = ucfirst(strtolower($detail->sex ?? 'Others'));
            if (!isset($totals[$sex])) $sex = 'Others';

            $value = (float)$detail->value;

            if ($this->isPercentage) {
                $totals[$sex] += $value;
                $counts[$sex]++;
            } else {
                $totals[$sex] += $value;
            }
        }

        // Calculate final values based on measurement unit
        if ($this->isPercentage) {
            foreach ($totals as $sex => $total) {
                if ($counts[$sex] > 0) {
                    $totals[$sex] = $total / $counts[$sex];
                    $this->totalSum += $totals[$sex];
                }
            }
        } else {
            $this->totalSum = array_sum($totals);
        }

        $this->chartData = [
            $totals['Male'],
            $totals['Female'],
            $totals['Others'],
        ];

        // Dispatch chart update with measurement unit info
        $this->dispatch('chart-updated', [
            'data' => $this->chartData,
            'measurementUnit' => $this->measurementUnit,
            'isPercentage' => $this->isPercentage
        ]);

        $this->loading = false;
    }

    public function updatedSelectedAdvocacy($value)
    {
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])->findOrFail($value);
        $this->selectedYear = $this->yearOptions[0] ?? '';
        $this->selectedAge = 'All Ages';
        $this->selectedSex = 'All Sexes';
        $this->selectedRegion = '';
        $this->updateChartData();
    }

    public function render()
    {
        return view('livewire.dashboard-index');
    }
}
