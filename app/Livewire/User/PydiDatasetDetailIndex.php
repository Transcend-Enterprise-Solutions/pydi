<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\{WithPagination, WithFileUploads};
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

    // create or edit dataset detail
    public $showModal = false;
    public $editMode = false;
    public $editId;

    // Fields
    public $dimension, $indicator, $region, $age, $sex, $value;
    public $gender = ['Male', 'Female', 'Others'];

    // Delete
    public $showDeleteModal = false;

    // Generate Template
    public $showFormatModal = false;
    public $selectedDimension;

    // Upload file
    public $showImportModal = false;
    public $file;

    // generate export
    public $showExportModal = false;

    public $dimensions = [], $indicators = [], $regions = [];
    public $datasetInfo = [];
    public $showEntries = 10;
    public $search = '';

    public function mount($id)
    {
        $this->datasetInfo = PydiDataset::find($id);
        $this->dimensions = Dimension::get();
        $this->indicators = Indicator::all();
        $this->regions = PhilippineRegions::get();
    }

    // Open for creating new
    public function create()
    {
        $this->reset(['dimension', 'indicator', 'region', 'age', 'sex', 'value']);
        $this->editId = null;
        $this->editMode = false;
        $this->showModal = true;
    }

    // Open for editing existing
    public function edit($id)
    {
        $detail = PydiDatasetDetail::findOrFail($id);

        $this->editId = $detail->id;
        $this->dimension = $detail->dimension_id;
        $this->indicator = $detail->indicator_id;
        $this->region = $detail->philippine_region_id;
        $this->age = $detail->age;
        $this->sex = $detail->sex;
        $this->value = $detail->value;

        $this->editMode = true;
        $this->showModal = true;
    }

    // Save (works for both Create and Update)
    public function save()
    {
        $this->validate([
            'dimension' => 'required|integer',
            'indicator' => 'required|integer',
            'region' => 'required|integer',
            'age' => 'nullable|string',
            'sex' => 'nullable|string',
            'value' => 'nullable|string',
        ]);

        PydiDatasetDetail::updateOrCreate(
            ['id' => $this->editId],
            [
                'pydi_dataset_id' => $this->datasetInfo['id'],
                'dimension_id' => $this->dimension,
                'indicator_id' => $this->indicator,
                'philippine_region_id' => $this->region,
                'age' => $this->age,
                'sex' => $this->sex,
                'value' => $this->value,
            ]
        );

        session()->flash('success', $this->editMode ? 'Dataset detail updated!' : 'New dataset added!');
        $this->showModal = false;
        $this->editMode = false;
    }

    // delete confirmation
    public function confirmDelete($id)
    {
        $this->editId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->editId) {
            PydiDatasetDetail::findOrFail($this->editId)->delete();
            session()->flash('success', 'Dataset deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'editId']);
    }

    // generate template for import
    public function downloadTemplate()
    {
        $this->validate([
            'selectedDimension' => 'required|integer|exists:dimensions,id',
        ]);

        $title = strtolower(Dimension::find($this->selectedDimension)->name ?? 'dataset template');

        $this->showFormatModal = false;

        return Excel::download(
            new PydiDatasetTemplateExport($this->selectedDimension),
            $title . '_template.xlsx'
        );
    }

    // import dataset details from file
    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240',
            'selectedDimension' => 'required|integer',
        ]);

        $path = $this->file->store('imports');
        $importer = new PydiDatasetDetailsImport(
            $this->datasetInfo['id'],
            $this->selectedDimension
        );

        try {
            Excel::import($importer, $path);

            if (!empty($importer->errors)) {
                $firstError = $importer->errors[0];
                $message = "Row Error: {$firstError['message']} | Row Data: " . json_encode($firstError['row']);
                session()->flash('error', $message);
            } else {
                session()->flash('success', 'Dataset details imported successfully!');
            }

            $this->reset('file', 'showImportModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function updatedDimension($value)
    {
        $this->indicators = [];
        $this->indicators = Indicator::where('dimension_id', $value)->get();
    }

    // generate export of dataset details
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


        $details = $query->latest()->paginate($this->showEntries);

        return view('livewire.user.pydi-dataset-detail-index', compact('details'));
    }
}
