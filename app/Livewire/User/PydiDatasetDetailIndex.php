<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\{WithPagination, WithFileUploads};
use Livewire\Attributes\{Title, Layout};
use App\Models\{PydiDatasetDetail, PydiDataset, Dimension, Indicator, PhilippineRegions, UserLog};
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PydiDatasetDetailsImport;
use App\Exports\{PydiDatasetDetailsExport, PydiDatasetTemplateExport};
use Illuminate\Support\Facades\DB;

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

    // Others fields
    public $dimensionOthersText = '';
    public $indicatorText = ''; // For free text input when dimension is "others"
    public $showDimensionOthers = false;
    public $showIndicatorAsText = false; // Show indicator as text input

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
        $this->datasetInfo = PydiDataset::with('indicator.dimension')->findOrFail($id);

        // Get all dimensions from admin - ordered with Others at the end
        $adminDimensions = Dimension::select('id', 'name')->orderBy('name')->get();

        $this->dimensions = collect();
        $othersDimension = null;

        // Separate regular dimensions from "Others"
        foreach ($adminDimensions as $dimension) {
            if (strtolower($dimension->name) === 'others') {
                $othersDimension = $dimension;
            } else {
                $this->dimensions->push((object)[
                    'id' => $dimension->id,
                    'name' => $dimension->name
                ]);
            }
        }

        // Add "Others" at the end if it exists
        if ($othersDimension) {
            $this->dimensions->push((object)[
                'id' => $othersDimension->id,
                'name' => 'Others (Please specify)'
            ]);
        }

        $this->regions = PhilippineRegions::select('id', 'region_description')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Watch for dimension changes
    public function updatedDimension($value)
    {
        // Reset indicator related fields
        $this->indicator = null;
        $this->indicatorText = '';
        $this->showDimensionOthers = false;
        $this->showIndicatorAsText = false;

        if ($value) {
            // Check if selected dimension is "Others"
            $selectedDimension = Dimension::find($value);

            if ($selectedDimension && strtolower($selectedDimension->name) === 'others') {
                // Show dimension others text field and indicator text input
                $this->showDimensionOthers = true;
                $this->showIndicatorAsText = true;
                $this->indicators = collect(); // Clear indicators dropdown
            } else {
                // Load indicators for selected dimension
                $indicatorData = Indicator::where('dimension_id', $value)
                    ->select('id', 'name')
                    ->get();

                $this->indicators = collect();
                foreach ($indicatorData as $indicator) {
                    $this->indicators->push((object)[
                        'id' => $indicator->id,
                        'name' => $indicator->name
                    ]);
                }

                $this->showIndicatorAsText = false;
            }
        } else {
            $this->indicators = collect();
        }
    }

    // Validation rules
    protected function rules()
    {
        $rules = [
            'region' => 'required|integer',
            'age' => 'nullable|string',
            'sex' => 'nullable|string',
            'value' => 'nullable|string',
        ];

        // Check if we're using the parent dataset's indicator or custom dimension
        if ($this->dimension) {
            // User manually selected a dimension (for "Others" case)
            $selectedDimension = Dimension::find($this->dimension);
            $isOthers = $selectedDimension && strtolower($selectedDimension->name) === 'others';

            if ($isOthers) {
                $rules['dimensionOthersText'] = 'required|string|max:255';
                $rules['indicatorText'] = 'required|string|max:255';
                $rules['dimension'] = 'required|integer|exists:dimensions,id';
            } else {
                $rules['dimension'] = 'required|integer|exists:dimensions,id';
                $rules['indicator'] = 'required|integer|exists:indicators,id';
            }
        }
        // If no dimension selected, we'll use the parent dataset's indicator (simplified flow)

        return $rules;
    }

    // Open for creating new
    public function create()
    {
        $this->reset(['dimension', 'indicator', 'region', 'age', 'sex', 'value', 'dimensionOthersText', 'indicatorText', 'showDimensionOthers', 'showIndicatorAsText']);
        $this->editId = null;
        $this->editMode = false;
        $this->showModal = true;
    }

    // Open for editing existing
    public function edit($id)
    {
        $detail = PydiDatasetDetail::findOrFail($id);

        $this->editId = $detail->id;
        $this->region = $detail->philippine_region_id;
        $this->age = $detail->age;
        $this->sex = $detail->sex;
        $this->value = $detail->value;

        // Check if this is an "others" dimension
        if ($detail->dimension_others_text) {
            $this->dimension = $detail->dimension_id; // Use the actual Others dimension ID
            $this->dimensionOthersText = $detail->dimension_others_text;
            $this->indicatorText = $detail->indicator_others_text;
            $this->showDimensionOthers = true;
            $this->showIndicatorAsText = true;
            $this->indicators = collect();
        } else {
            $this->dimension = $detail->dimension_id;
            $this->indicator = $detail->indicator_id;
            $this->showDimensionOthers = false;
            $this->showIndicatorAsText = false;

            // Load indicators for the dimension
            if ($this->dimension) {
                $indicatorData = Indicator::where('dimension_id', $this->dimension)
                    ->select('id', 'name')
                    ->get();

                $this->indicators = collect();
                foreach ($indicatorData as $indicator) {
                    $this->indicators->push((object)[
                        'id' => $indicator->id,
                        'name' => $indicator->name
                    ]);
                }
            }
        }

        $this->editMode = true;
        $this->showModal = true;
    }

    // Save (works for both Create and Update)
    public function save()
    {
        $this->validate();

        $data = [
            'pydi_dataset_id' => $this->datasetInfo['id'],
            'philippine_region_id' => $this->region,
            'age' => $this->age,
            'sex' => $this->sex,
            'value' => $this->value,
        ];

        // Check if user manually selected a dimension (for "Others" custom entries)
        if ($this->dimension) {
            $selectedDimension = Dimension::find($this->dimension);
            $isOthers = $selectedDimension && strtolower($selectedDimension->name) === 'others';

            if ($isOthers) {
                // Save with Others dimension ID and custom text
                $data['dimension_id'] = $this->dimension;
                $data['indicator_id'] = null;
                $data['dimension_others_text'] = $this->dimensionOthersText;
                $data['indicator_others_text'] = $this->indicatorText;
            } else {
                // Save with regular dimension/indicator IDs
                $data['dimension_id'] = $this->dimension;
                $data['indicator_id'] = $this->indicator;
                $data['dimension_others_text'] = null;
                $data['indicator_others_text'] = null;
            }
        } else {
            // Use parent dataset's indicator (simplified flow from the image)
            $data['dimension_id'] = $this->datasetInfo->indicator->dimension_id ?? null;
            $data['indicator_id'] = $this->datasetInfo->indicator_id ?? null;
            $data['dimension_others_text'] = null;
            $data['indicator_others_text'] = null;
        }

        PydiDatasetDetail::updateOrCreate(
            ['id' => $this->editId],
            $data
        );

        // Log the action
        $action = $this->editMode ? 'Updated' : 'Created';
        $dimensionName = $data['dimension_others_text'] ?? ($selectedDimension->name ?? 'from parent dataset');
        $this->logs("{$action} dataset detail for dimension: {$dimensionName}");

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
            $detail = PydiDatasetDetail::findOrFail($this->editId);
            $dimensionName = $detail->dimension_others_text ?: ($detail->dimension->name ?? 'Unknown');

            $detail->delete();
            $this->logs("Deleted dataset detail for dimension: {$dimensionName}");
            session()->flash('success', 'Dataset deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'editId']);
    }

    // generate template for import
    public function downloadTemplate()
    {
        $this->validate([
            'selectedDimension' => 'required',
        ]);

        $selectedDimension = Dimension::find($this->selectedDimension);

        // Generate filename based on dimension name
        if ($selectedDimension) {
            // Convert dimension name to slug format
            $dimensionSlug = strtolower($selectedDimension->name);
            $dimensionSlug = preg_replace('/[^a-z0-9]+/', '_', $dimensionSlug);
            $dimensionSlug = trim($dimensionSlug, '_');
            
            $filename = $dimensionSlug . '_template.xlsx';
        } else {
            $filename = 'dataset_template.xlsx';
        }

        $this->showFormatModal = false;

        return Excel::download(
            new PydiDatasetTemplateExport($this->selectedDimension),
            $filename
        );
    }

    // import dataset details from file
    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240',
            'selectedDimension' => 'required',
        ]);

        $path = $this->file->store('imports');

        try {
            $importer = new PydiDatasetDetailsImport(
                $this->datasetInfo['id'],
                $this->selectedDimension
            );

            Excel::import($importer, $path);

            if (!empty($importer->errors)) {
                $firstError = $importer->errors[0];
                $message = "Row Error: {$firstError['message']} | Row Data: " . json_encode($firstError['row']);
                session()->flash('error', $message);
            } else {
                $this->logs("Imported dataset details from file");
                session()->flash('success', 'Dataset details imported successfully!');
            }

            $this->reset(['file', 'showImportModal', 'selectedDimension']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    // generate export of dataset details
    public function export($type = 'csv')
    {
        // Create export instance
        $export = new PydiDatasetDetailsExport($this->datasetInfo->id);
        
        // Generate dynamic filename based on indicator name
        $indicatorSlug = $export->getIndicatorSlug();
        $filename = $indicatorSlug . '.' . $type;

        $this->showExportModal = false;

        $this->logs("Exported dataset details as {$type}");

        return Excel::download($export, $filename);
    }

    public function logs($action)
    {
        UserLog::create([
            'user_id' => auth()->id(),
            'action' => $action . ' at ' . now()->format('Y-m-d H:i:s'),
        ]);
    }

    // Safe method to handle bulk operations without queueing mixed models
    public function processBulkData($data)
    {
        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                $this->processIndividualItem($item);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Bulk operation completed successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Error in bulk operation: ' . $e->getMessage()];
        }
    }

    private function processIndividualItem($item)
    {
        if (is_array($item)) {
            return $this->processArrayItem($item);
        } elseif ($item instanceof \Illuminate\Database\Eloquent\Model) {
            return $this->processModelItem($item->toArray());
        }

        return false;
    }

    private function processArrayItem($data)
    {
        return PydiDatasetDetail::create($data);
    }

    private function processModelItem($data)
    {
        return PydiDatasetDetail::create($data);
    }

    public function render()
    {
        $query = PydiDatasetDetail::with(['region', 'dimension', 'indicator'])
            ->where('pydi_dataset_id', $this->datasetInfo['id'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('sex', 'like', "%{$this->search}%")
                        ->orWhere('age', 'like', "%{$this->search}%")
                        ->orWhere('value', 'like', "%{$this->search}%")
                        ->orWhere('dimension_others_text', 'like', "%{$this->search}%")
                        ->orWhere('indicator_others_text', 'like', "%{$this->search}%")
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

        return view('livewire.user.pydi-dataset-detail-index', compact('details'));
    }
}