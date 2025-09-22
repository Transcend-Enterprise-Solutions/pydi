<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension, PydiDataset, Indicator, PhilippineRegion, PhilippineRegions};

#[Layout('layouts.land')]
#[Title('PYDI Home')]
class AdvocacyIndex extends Component
{
    public $advocacyInfo;
    public $yearOptions = [];
    public $demensions = [];
    public $indicators = [];
    public $regions = [];
    public $ageOptions = ['15-17', '18-24', '25-30 ', 'All Ages'];
    public $sexOptions = ['Male', 'Female', 'Others', 'All Sexes'];

    public $selectedAge = 'All Ages';
    public $selectedSex = 'All Sexes';
    public $selectedRegion = '';
    public $selectedIndicator = '';
    public $selectedYear = '';
    public $selectedAdvocacy = '';

    public $chartLabels = ['Male', 'Female', 'Others'];
    public $chartData = [0, 0, 0];
    public $totalSum = 0;
    public $loading = false;

    // New properties for measurement unit handling
    public $measurementUnit = 'frequency'; // default
    public $isPercentage = false;

    public function mount($id)
    {
        $this->demensions = Dimension::orderBy('name')->get()->toArray();
        $this->regions = PhilippineRegions::orderBy('region_description')->get()->toArray();

        $this->selectedAdvocacy = $id;
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])->findOrFail($id);

        // Load indicators for the selected advocacy
        $this->indicators = $this->advocacyInfo->indicators->toArray();

        // Select the first indicator if available and set measurement unit
        if (!empty($this->indicators)) {
            $this->selectedIndicator = $this->indicators[0]['id'];
            $this->measurementUnit = $this->indicators[0]['measurement_unit'] ?? 'frequency';
            $this->isPercentage = $this->measurementUnit === 'percentage';
        }

        // Fetch unique years from PydiDataset (descending order)
        $this->yearOptions = PydiDataset::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Default year is the current year if available, otherwise the latest year from the DB
        $this->selectedYear = in_array(date('Y'), $this->yearOptions)
            ? date('Y')
            : ($this->yearOptions[0] ?? null);

        // Set default region to "All Regions"
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
            // If no specific indicator selected, use first indicator or default
            if (!empty($this->indicators)) {
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

    protected function updateChartData()
    {
        $this->loading = true;

        $datasetDetails = Dimension::with(['pydiDatasetDetails' => function ($query) {
            // Age filter
            if ($this->selectedAge !== "All Ages") {
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

            // Indicator filter
            if (!empty($this->selectedIndicator)) {
                $query->where('indicator_id', $this->selectedIndicator);
            }

            // Year and status filter
            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('year', $this->selectedYear)
                    ->where('status', 'approved');
            });
        }, 'pydiDatasetDetails.pydiDataset'])
            ->where('id', $this->advocacyInfo->id)
            ->first();

        if ($datasetDetails && $datasetDetails->pydiDatasetDetails->isNotEmpty()) {
            $totals = ['Male' => 0, 'Female' => 0, 'Others' => 0];
            $counts = ['Male' => 0, 'Female' => 0, 'Others' => 0]; // For percentage calculation
            $this->totalSum = 0;

            foreach ($datasetDetails->pydiDatasetDetails as $detail) {
                $sex = ucfirst(strtolower($detail->sex ?? 'Others'));
                if (!isset($totals[$sex])) {
                    $sex = 'Others';
                }

                $value = (float) $detail->value;

                if ($this->isPercentage) {
                    // For percentage, we might want to average or handle differently
                    $totals[$sex] += $value;
                    $counts[$sex]++;
                } else {
                    // For frequency, sum as before
                    $totals[$sex] += $value;
                }
            }

            // Calculate final values based on measurement unit
            if ($this->isPercentage) {
                // For percentage, calculate average if multiple entries
                foreach ($totals as $sex => $total) {
                    if ($counts[$sex] > 0) {
                        $totals[$sex] = $total / $counts[$sex];
                        $this->totalSum += $totals[$sex];
                    }
                }
            } else {
                // For frequency, use sum as before
                $this->totalSum = array_sum($totals);
            }

            $this->chartData = [
                $totals['Male'],
                $totals['Female'],
                $totals['Others'],
            ];
        } else {
            $this->chartData = [0, 0, 0];
            $this->totalSum = 0;
        }

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
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])
            ->findOrFail($value);

        // Load indicators for the new advocacy
        $this->indicators = $this->advocacyInfo->indicators->toArray();

        // Reset filters and select first indicator
        if (!empty($this->indicators)) {
            $this->selectedIndicator = $this->indicators[0]['id'];
            $this->measurementUnit = $this->indicators[0]['measurement_unit'] ?? 'frequency';
            $this->isPercentage = $this->measurementUnit === 'percentage';
        } else {
            $this->selectedIndicator = '';
            $this->measurementUnit = 'frequency';
            $this->isPercentage = false;
        }

        // Reset other filters
        $this->selectedYear = $this->yearOptions[0] ?? '';
        $this->selectedAge = 'All Ages';
        $this->selectedSex = 'All Sexes';
        $this->selectedRegion = '';

        // Update chart data based on the new advocacy
        $this->updateChartData();
    }

    public function render()
    {
        return view('livewire.landing.advocacy-index', [
            'chartLabels' => $this->chartLabels,
            'chartData'   => $this->chartData,
        ]);
    }
}
