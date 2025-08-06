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
    public $dimensions = [];
    public $ageOptions = ['1-14', '15-24', '25-34', '35-44', '45-54', '55-64', '65+', 'All Ages'];
    public $selectedAge = 'All Ages';
    public $selectedYear = '';
    public $selectedDimension = "";
    public $selectedAdvocacy = '';

    public $chartLabels = ['Male', 'Female', 'Others'];
    public $chartData = [0, 0, 0];
    public $totalSum = 0;
    public $loading = false;

    public function mount()
    {
        $id = Dimension::orderBy('id', 'asc')->first()?->id;
        $this->dimensions = Dimension::orderBy('name')->get();

        $this->selectedAdvocacy = $id;
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])->findOrFail($id);


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

        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])
            ->findOrFail($value);

        $this->updateChartData();
        $this->loading = false;
    }

    protected function updateChartData()
    {
        $this->loading = true;

        $datasetDetails = Dimension::with(['pydiDatasetDetails' => function ($query) {
            if ($this->selectedAge !== "All Ages") {
                if (str_contains($this->selectedAge, '+')) {
                    $ageMin = rtrim($this->selectedAge, '+');
                    $query->where('age', '>=', $ageMin);
                } else {
                    [$min, $max] = explode('-', $this->selectedAge);
                    $query->whereBetween('age', [(int)$min, (int)$max]);
                }
            }

            if (auth()->user()->user_role === 'user') {
                $query->whereHas('pydiDataset', function ($subQuery) {
                    $subQuery->where('user_id', auth()->id());
                });
            }

            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('year', $this->selectedYear);
                $subQuery->where('status', 'approved');
            });
        }, 'pydiDatasetDetails.pydiDataset'])
            ->where('id', $this->advocacyInfo->id)
            ->first();

        if ($datasetDetails && $datasetDetails->pydiDatasetDetails->isNotEmpty()) {
            $totals = ['Male' => 0, 'Female' => 0, 'Others' => 0];
            $this->totalSum = 0;

            foreach ($datasetDetails->pydiDatasetDetails as $detail) {
                $sex = ucfirst(strtolower($detail->sex ?? 'Others'));

                if (!isset($totals[$sex])) {
                    $sex = 'Others';
                }

                $value = (int) $detail->value;
                $totals[$sex] += $value;
                $this->totalSum += $value;
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

        $this->dispatch('chart-updated', data: $this->chartData);
        $this->loading = false;
    }

    public function updatedSelectedAdvocacy($value)
    {
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetails'])
            ->findOrFail($value);

        // Reset year and age filters
        $this->selectedYear = $this->yearOptions[0] ?? '';
        $this->selectedAge = 'All Ages';

        // Update chart data based on the new advocacy
        $this->updateChartData();
    }

    public function render()
    {
        return view('livewire.dashboard-index');
    }
}
