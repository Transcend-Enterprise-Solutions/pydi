<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\{WithPagination, WithFileUploads};
use App\Models\{PydpDatasetDetail, PydpDataset};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatasetDetailExport;

#[Layout('layouts.app')]
#[Title('PYDP Dataset Details')]
class ManagePydpDetailIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $datasetInfo = [], $yearRange = [];
    public $showEntries = 10;
    public $search = '';

    public $dimension, $indicator_id;
    public $yearData = [];
    public $valueId;
    public $remarks;

    public $showModal = false;
    public $editMode = false;

    public function mount($id)
    {
        $dataset = PydpDataset::with('type')->findOrFail($id);
        $this->datasetInfo = $dataset;



        $years = range((int)$dataset->type->year_start, (int)$dataset->type->year_end);
        $this->yearRange = array_map('strval', $years);
    }

    public function exportDatasetDetails()
    {
        $yearStart = $this->datasetInfo['type']->year_start;
        $yearEnd = $this->datasetInfo['type']->year_end;

        $yearRange = range($yearStart, $yearEnd);

        return Excel::download(new DatasetDetailExport($yearRange, $this->datasetInfo['id']), 'dataset_details.xlsx');
    }

    public function render()
    {
        $query = PydpDatasetDetail::with(['indicator', 'pydpDataset', 'dimension'])
            ->where('pydp_dataset_id', $this->datasetInfo['id'])
            ->when($this->search, function ($q) {
                $q->whereHas('indicator', function ($q2) {
                    $q2->where('title', 'like', '%' . $this->search . '%');
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.admin.manage-pydp-detail-index', compact('tableDatas'));
    }
}
