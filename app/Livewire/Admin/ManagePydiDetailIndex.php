<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Title, Layout};
use App\Models\{PydiDatasetDetail, PydiDataset};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PydiDatasetDetailsExport;

#[Layout('layouts.app')]
#[Title('PYDI Dataset Details')]
class ManagePydiDetailIndex extends Component
{
    use WithPagination;

    public $datasetInfo;
    public $search = '';
    public $showEntries = 10;
    public $showExportModal = false;

    public function mount($id)
    {
        // Check if dataset exists (including soft-deleted records if needed)
        $this->datasetInfo = PydiDataset::with(['user', 'indicator.dimension'])
            // ->withTrashed() // Uncomment if you need to access soft-deleted records
            ->find($id);
        
        // If dataset not found, redirect back with error message
        if (!$this->datasetInfo) {
            session()->flash('error', 'Dataset not found or has been deleted.');
            $this->redirect(route('manage-pydi-datasets'), navigate: true);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function export($type = 'csv')
    {
        $this->validate([
            'showExportModal' => 'boolean',
        ]);

        $filename = 'pydi_dataset_details_' . now()->format('Ymd_His') . '.' . $type;

        $this->showExportModal = false;
        
        try {
            return Excel::download(
                new PydiDatasetDetailsExport($this->datasetInfo->id), 
                $filename
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export dataset details. Please try again.');
            return null;
        }
    }

    public function render()
    {
        // Load dataset details with all necessary relationships
        $query = PydiDatasetDetail::with(['region', 'dimension', 'indicator'])
            ->where('pydi_dataset_id', $this->datasetInfo->id)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('sex', 'like', "%{$this->search}%")
                        ->orWhere('age', 'like', "%{$this->search}%")
                        ->orWhere('value', 'like', "%{$this->search}%")
                        ->orWhere('indicator_others_text', 'like', "%{$this->search}%")
                        ->orWhere('dimension_others_text', 'like', "%{$this->search}%")
                        ->orWhereHas('region', function($region) {
                            $region->where('region_description', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('dimension', function($dimension) {
                            $dimension->where('name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('indicator', function($indicator) {
                            $indicator->where('name', 'like', "%{$this->search}%");
                        });
                });
            });

        $details = $query->latest()->paginate($this->showEntries);

        return view('livewire.admin.manage-pydi-detail-index', compact('details'));
    }
}