<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension, PydiDataset};

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
        } else {
            $this->advocacyInfo = Dimension::with('indicators')->findOrFail($value);
            $this->indicators = $this->advocacyInfo->indicators;
        }

        $this->updateChartData();
        $this->loading = false;
    }

    public function updatedSelectedIndicator($value)
    {
        $this->loading = true;
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
        $this->totalSum = 0;

        // Sum values for all selected dimensions
        foreach ($datasetDetails as $dimension) {
            foreach ($dimension->pydiDatasetDetails as $detail) {
                $sex = ucfirst(strtolower($detail->sex ?? 'Others'));
                if (!isset($totals[$sex])) $sex = 'Others';

                $value = (int)$detail->value;
                $totals[$sex] += $value;
                $this->totalSum += $value;
            }
        }

        $this->chartData = [
            $totals['Male'],
            $totals['Female'],
            $totals['Others'],
        ];

        $this->dispatch('chart-updated', data: $this->chartData);
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
