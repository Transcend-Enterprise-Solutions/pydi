<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension, PydiDataset, Indicator};

#[Layout('layouts.app')]
#[Title('Dashboard')]
class DashboardIndex extends Component
{
    public $advocacyInfo;
    public $yearOptions = [];
    public $indicators = [];
    public $dimensions = [];
    public $ageOptions = ['15-17', '18-24', '25-30 ', 'All Ages'];
    public $selectedAge = 'All Ages';
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
        $this->dimensions = Dimension::orderBy('name')->get();
        $this->selectedDimension = "";

        $this->yearOptions = PydiDataset::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $this->selectedYear = in_array(date('Y'), $this->yearOptions) ? date('Y') : ($this->yearOptions[0] ?? null);

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

    public function updatedSelectedDimension($value)
    {
        $this->loading = true;

        if (empty($value)) {
            $this->advocacyInfo = null; // All dimensions
            $this->indicators = [];
            $this->measurementUnit = 'frequency'; // Reset to default
            $this->isPercentage = false;
        } else {
            $this->advocacyInfo = Dimension::with('indicators')->findOrFail($value);
            $this->indicators = $this->advocacyInfo->indicators;
            // Set measurement unit based on first indicator if available
            if (!empty($this->indicators)) {
                $firstIndicator = is_array($this->indicators) ? reset($this->indicators) : $this->indicators->first();
                $this->measurementUnit = $firstIndicator->measurement_unit ?? 'frequency';
                $this->isPercentage = $this->measurementUnit === 'percentage';
            }
        }

        $this->selectedIndicator = ""; // Reset indicator selection
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
                $firstIndicator = is_array($this->indicators) ? reset($this->indicators) : $this->indicators->first();
                $this->measurementUnit = $firstIndicator->measurement_unit ?? 'frequency';
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

        // Base query: fetch all or selected dimensions
        $dimensionsQuery = Dimension::with(['pydiDatasetDetails' => function ($query) {
            if ($this->selectedAge !== 'All Ages') {
                if (str_contains($this->selectedAge, '+')) {
                    $ageMin = rtrim($this->selectedAge, '+');
                    $query->where('age', '>=', $ageMin);
                } else {
                    [$min, $max] = explode('-', $this->selectedAge);
                    $query->whereBetween('age', [(int)$min, (int)$max]);
                }
            }

            if (!empty($this->selectedIndicator)) {
                $query->where('indicator_id', $this->selectedIndicator);
            }

            if (auth()->user()->user_role === 'user') {
                $query->whereHas('pydiDataset', function ($subQuery) {
                    $subQuery->where('user_id', auth()->id());
                });
            }

            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('year', $this->selectedYear)
                    ->where('status', 'approved');
            });
        }, 'pydiDatasetDetails.pydiDataset']);

        if (!empty($this->selectedDimension)) {
            $dimensionsQuery->where('id', $this->selectedDimension);
        }

        $datasetDetails = $dimensionsQuery->get();

        // Initialize totals
        $totals = ['Male' => 0, 'Female' => 0, 'Others' => 0];
        $counts = ['Male' => 0, 'Female' => 0, 'Others' => 0]; // For percentage calculation
        $this->totalSum = 0;

        // Sum values for all selected dimensions
        foreach ($datasetDetails as $dimension) {
            foreach ($dimension->pydiDatasetDetails as $detail) {
                $sex = ucfirst(strtolower($detail->sex ?? 'Others'));
                if (!isset($totals[$sex])) $sex = 'Others';

                $value = (float)$detail->value;

                if ($this->isPercentage) {
                    // For percentage, we might want to average or handle differently
                    // This depends on your business logic
                    $totals[$sex] += $value;
                    $counts[$sex]++;
                } else {
                    // For frequency, sum as before
                    $totals[$sex] += $value;
                }
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
        $this->updateChartData();
    }

    public function render()
    {
        return view('livewire.dashboard-index');
    }
}
