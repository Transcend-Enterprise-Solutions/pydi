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
    public $ageOptions = ['1-14', '15-24', '25-34', '35-44', '45-54', '55-64', '65+', 'All Ages'];
    public $selectedAge = 'All Ages';
    public $selectedYear = '';

    public $chartLabels = ['Male', 'Female', 'Others'];
    public $chartData = [0, 0, 0];

    public function mount($id)
    {
        $this->advocacyInfo = Dimension::with(['indicators', 'pydiDatasetDetals'])->findOrFail($id);

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
        $datasetDetails = Dimension::with(['pydiDatasetDetals' => function ($query) {
            if ($this->selectedAge !== "All Ages") {
                [$min, $max] = explode('-', $this->selectedAge);

                if (str_contains($this->selectedAge, '+')) {
                    $query->where('age', '>=', rtrim($this->selectedAge, '+'));
                } else {
                    $query->whereBetween('age', [(int)$min, (int)$max]);
                }
            }

            $query->whereHas('pydiDataset', function ($subQuery) {
                $subQuery->where('year', $this->selectedYear);
            });
        }, 'pydiDatasetDetals.pydiDataset'])
            ->where('id', $this->advocacyInfo->id)
            ->first();

        if ($datasetDetails && $datasetDetails->pydiDatasetDetals->isNotEmpty()) {
            // Initialize counts for each sex category
            $counts = ['Male' => 0, 'Female' => 0, 'Others' => 0];

            // Sum values per sex
            foreach ($datasetDetails->pydiDatasetDetals as $detail) {
                $sex = $detail->sex ?? 'Others';
                $counts[$sex] = isset($counts[$sex]) ? $counts[$sex] + 1 : 1;
            }

            // Assign counts to chartData
            $this->chartData = [
                $counts['Male'],
                $counts['Female'],
                $counts['Others']
            ];
        } else {
            $this->chartData = [0, 0, 0];
        }

        // Notify AlpineJS to update the chart dynamically
        $this->dispatch('chart-updated', data: $this->chartData);
    }

    public function render()
    {
        return view('livewire.landing.advocacy-index', [
            'chartLabels' => $this->chartLabels,
            'chartData'   => $this->chartData,
        ]);
    }
}
