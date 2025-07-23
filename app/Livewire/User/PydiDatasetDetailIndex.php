<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\{Title, Layout};
use App\Models\{PydiDatasetDetail, PydiDataset, Dimension, Indicator, PhilippineRegions};
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PydiDatasetDetailsImport;
use App\Exports\{PydiDatasetDetailsExport, PydiDatasetTemplateExport};

#[Layout('layouts.app')]
#[Title('PYDI Dataset Details')]
class PydiDatasetDetailIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $editMode = false;
    public $editId;

    public $showDeleteModal = false;
    public $datasetId;

    // Editable fields
    public $edit_dimension;
    public $edit_indicator;
    public $edit_region;
    public $edit_age;
    public $edit_sex;
    public $edit_content;
    public $gender = ['Male', 'Female', 'Others'];

    public $dimensions = [], $indicators = [], $regions = [];
    public $datasetInfo = [];
    public $showEntries = 10;
    public $search = '';
    public $showImportModal = false;
    public $showExportModal = false;
    public $file;

    public $pydi_dataset_id, $dimension_id, $indicator_id, $region_id, $sex, $age, $content;

    public function mount($id)
    {
        $this->datasetInfo = PydiDataset::find($id);
        $this->dimensions = Dimension::get();
        $this->indicators = Indicator::all();
        $this->regions = PhilippineRegions::get();
    }

    public function edit($id)
    {
        $detail = PydiDatasetDetail::findOrFail($id);
        $this->editId = $id;

        $this->edit_dimension = $detail->dimension_id;
        $this->edit_indicator = $detail->indicator_id;
        $this->edit_region = $detail->philippine_region_id;
        $this->edit_age = $detail->age;
        $this->edit_sex = $detail->sex;
        $this->edit_content = $detail->content;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function updatedEditDimension($value)
    {
        $this->indicators = Indicator::where('dimension_id', $value)->get();
        $this->edit_indicator = null;
    }

    public function save()
    {
        $this->validate([
            'edit_dimension' => 'required|integer',
            'edit_indicator' => 'required|integer',
            'edit_region' => 'required|integer',
            'edit_age' => 'nullable|string',
            'edit_sex' => 'nullable|string',
            'edit_content' => 'nullable|string',
        ]);

        PydiDatasetDetail::updateOrCreate(
            ['id' => $this->editId],
            [
                'dimension_id' => $this->edit_dimension,
                'indicator_id' => $this->edit_indicator,
                'philippine_region_id' => $this->edit_region,
                'age' => $this->edit_age,
                'sex' => $this->edit_sex,
                'content' => $this->edit_content,
            ]
        );

        session()->flash('success', $this->editMode ? 'Dataset detail updated!' : 'New dataset added!');
        $this->editMode = false;
        $this->showModal = false;
    }

    public function delete()
    {
        if ($this->datasetId) {
            PydiDatasetDetail::findOrFail($this->datasetId)->delete();
            session()->flash('success', 'Dataset deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'datasetId']);
    }

    public function confirmDelete($id)
    {
        $this->datasetId = $id;
        $this->showDeleteModal = true;
    }

    public function downloadTemplate()
    {
        return Excel::download(new PydiDatasetTemplateExport, 'pydi_dataset_template.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240'
        ]);

        $path = $this->file->store('imports');

        try {
            Excel::import(new PydiDatasetDetailsImport($this->datasetInfo['id']), $path);

            $this->reset('file', 'showImportModal');
            session()->flash('success', 'Dataset details imported successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function export($type = 'csv')
    {
        $filename = 'pydi_dataset_details_' . now()->format('Ymd_His') . '.' . $type;
        return Excel::download(new PydiDatasetDetailsExport($this->datasetInfo['id']), $filename);
    }

    public function saveDetail()
    {
        $this->validate([
            'pydi_dataset_id' => 'required|integer',
            'dimension_id' => 'required|integer',
            'indicator_id' => 'required|integer',
            'region_id' => 'required|integer',
            'sex' => 'required|string',
            'age' => 'required|string',
        ]);

        \App\Models\PydiDatasetDetail::create([
            'pydi_dataset_id' => $this->pydi_dataset_id,
            'dimension_id' => $this->dimension_id,
            'indicator_id' => $this->indicator_id,
            'philippine_region_id' => $this->region_id,
            'sex' => $this->sex,
            'age' => $this->age,
            'content' => $this->content ?? null,
        ]);

        $this->reset(['pydi_dataset_id', 'dimension_id', 'indicator_id', 'region_id', 'sex', 'age', 'content', 'showCreateModal']);
        session()->flash('success', 'Dataset detail added!');
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

        return view('livewire.user.pydi-dataset-detail-index', compact('details'));
    }
}
