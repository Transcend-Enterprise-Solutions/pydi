<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension, PydiDataset};

#[Layout('layouts.land')]
#[Title('PYDI Home')]
class AdvocacyIndex extends Component
{
    public $advocacyInfo;
    public $yearOptions = [];
    public $demensions = [];
    public $ageOptions = ['15-17', '18-24', '25-30 ', 'All Ages'];
    public $selectedAge = 'All Ages';
    public $selectedYear = '';
    public $selectedAdvocacy = '';

    public $chartLabels = ['Male', 'Female', 'Others'];
    public $chartData = [0, 0, 0];
    public $totalSum = 0;

    public function mount($id)
    {
        $this->demensions = Dimension::all();
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

    protected function updateChartData()
    {
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

            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('year', $this->selectedYear);
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
        return view('livewire.landing.advocacy-index', [
            'chartLabels' => $this->chartLabels,
            'chartData'   => $this->chartData,
        ]);
    }
}
