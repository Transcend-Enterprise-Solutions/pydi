<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\{Title, Layout};
use App\Models\{PydiDatasetDetail, PydiDataset};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{PydiDatasetDetailsExport};

#[Layout('layouts.app')]
#[Title('PYDI Dataset Details')]
class ManagePydiDetailIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $datasetInfo = [];
    public $showEntries = 10;
    public $search = '';
    public $showExportModal = false;

    public function mount($id)
    {
        $this->datasetInfo = PydiDataset::find($id);
    }

    public function export($type = 'csv')
    {
        $filename = 'pydi_dataset_details_' . now()->format('Ymd_His') . '.' . $type;

        $this->showExportModal = false;
        return Excel::download(new PydiDatasetDetailsExport($this->datasetInfo['id']), $filename);
    }

    public function render()
    {
        $query = PydiDatasetDetail::with(['region', 'dimension', 'indicator'])
            ->where('pydi_dataset_id', $this->datasetInfo['id'])
            ->when($this->search, function ($q) {
                $q->where('sex', 'like', "%{$this->search}%")
                    ->orWhere('age', 'like', "%{$this->search}%")
                    ->orWhereHas('region', fn($ds) => $ds->where('region_description', 'like', "%{$this->search}%"));
            });

        $details = $query->paginate($this->showEntries);

        return view('livewire.admin.manage-pydi-detail-index', compact('details'));
    }
}
