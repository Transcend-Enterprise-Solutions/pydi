<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Illuminate\Support\Facades\DB;
use Livewire\{WithPagination, WithFileUploads};
use App\Models\{PydpDatasetDetail, PydpDataset, Dimension, PydpYear, UserLog, PydpLevel, PydpIndicator, PydpType};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatasetDetailExport;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
#[Title('PYDP Dataset Detail')]
class PydpDatasetDetailIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $datasetInfo = [], $allIndicators = [], $dimensions = [], $yearRange = [], $levels = [], $types = [];
    public $indicators = [];
    public $showEntries = 10;
    public $search = '';

    public $dimension, $indicator_id, $level_id, $type_id;
    public $yearData = [];
    public $valueId;

    public $showDeleteModal = false;
    public $showModal = false;
    public $editMode = false;

    public function mount($id)
    {
        $dataset = PydpDataset::with(['type'])->findOrFail($id);
        $this->datasetInfo = $dataset;

        // Get all levels, types, and indicators
        $this->levels = PydpLevel::all();
        $this->types = PydpType::all();
        $this->allIndicators = PydpIndicator::all();
        $this->dimensions = Dimension::all();

        $years = range((int)$dataset->type->year_start, (int)$dataset->type->year_end);
        $this->yearRange = array_map('strval', $years);
    }

    protected $rules = [
        'level_id' => 'required|exists:pydp_levels,id',
        'dimension' => 'required|exists:dimensions,id',
        'indicator_id' => 'required|exists:pydp_indicators,id',
        'yearData' => 'nullable|array',
        'yearData.*.physical_target' => 'nullable|numeric',
        'yearData.*.financial_target' => 'nullable|numeric',
        'yearData.*.physical_actual' => 'nullable|numeric',
        'yearData.*.financial_actual' => 'nullable|numeric',
        'yearData.*.baseline' => 'nullable|numeric|min:0',
        'yearData.*.total' => 'nullable|numeric|min:0',
        'yearData.*.remarks' => 'nullable|string|max:1000',
    ];

    // Computed property to get filtered indicators based on selected level
    public function getIndicatorsProperty()
    {
        if ($this->level_id) {
            return PydpIndicator::where('pydp_level_id', $this->level_id)->get();
        }

        return $this->allIndicators;
    }

    // Update indicators when level changes
    public function updatedLevelId($value)
    {
        // Reset indicator when level changes
        $this->indicator_id = null;

        if ($value) {
            $this->indicators = PydpIndicator::where('pydp_level_id', $value)->get();
        } else {
            $this->indicators = $this->allIndicators;
        }
    }

    public function create()
    {
        $this->reset(['level_id', 'type_id', 'dimension', 'indicator_id', 'yearData', 'valueId']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $data = PydpDatasetDetail::with(['years', 'indicator.level'])->findOrFail($id);

        $this->valueId = $data->id;
        $this->level_id = $data->indicator->pydp_level_id ?? null; // Get level from indicator
        $this->type_id = $data->indicator->pydp_type_id ?? null;
        $this->dimension = $data->dimension_id;
        $this->indicator_id = $data->pydp_indicator_id;

        // Populate year data with new fields
        $this->yearData = [];
        foreach ($data->years as $year) {
            $this->yearData[$year->year] = [
                'physical_target' => $year->target_physical,
                'financial_target' => $year->target_financial,
                'physical_actual' => $year->actual_physical,
                'financial_actual' => $year->actual_financial,
                'baseline' => $year->baseline,
                'total' => $year->total,
                'remarks' => $year->remarks,
            ];
        }

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        Log::info('Save method called', [
            'level_id' => $this->level_id,
            'dimension' => $this->dimension,
            'indicator_id' => $this->indicator_id,
            'yearData' => $this->yearData
        ]);

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->editMode) {
                // Update dataset detail (only basic info now)
                PydpDatasetDetail::where('id', $this->valueId)->update([
                    'pydp_dataset_id' => $this->datasetInfo['id'],
                    'dimension_id' => $this->dimension,
                    'pydp_indicator_id' => $this->indicator_id,
                ]);

                // Delete old years and reinsert
                PydpYear::where('pydp_dataset_detail_id', $this->valueId)->delete();

                // Only create year records if yearData exists
                if (!empty($this->yearData)) {
                    foreach ($this->yearData as $year => $values) {
                        PydpYear::create([
                            'pydp_dataset_detail_id' => $this->valueId,
                            'year' => $year,
                            'target_physical' => $values['physical_target'] ?? null,
                            'target_financial' => $values['financial_target'] ?? null,
                            'actual_physical' => $values['physical_actual'] ?? null,
                            'actual_financial' => $values['financial_actual'] ?? null,
                            'baseline' => $values['baseline'] ?? null,
                            'total' => $values['total'] ?? null,
                            'remarks' => $values['remarks'] ?? null,
                        ]);
                    }
                }

                $this->logs("Updated dataset detail ID: {$this->valueId}");
            } else {
                // Create new detail
                $detail = PydpDatasetDetail::create([
                    'pydp_dataset_id' => $this->datasetInfo['id'],
                    'dimension_id' => $this->dimension,
                    'pydp_indicator_id' => $this->indicator_id,
                ]);

                // Only create year records if yearData exists
                if (!empty($this->yearData)) {
                    foreach ($this->yearData as $year => $values) {
                        PydpYear::create([
                            'pydp_dataset_detail_id' => $detail->id,
                            'year' => $year,
                            'target_physical' => $values['physical_target'] ?? null,
                            'target_financial' => $values['financial_target'] ?? null,
                            'actual_physical' => $values['physical_actual'] ?? null,
                            'actual_financial' => $values['financial_actual'] ?? null,
                            'baseline' => $values['baseline'] ?? null,
                            'total' => $values['total'] ?? null,
                            'remarks' => $values['remarks'] ?? null,
                        ]);
                    }
                }

                $this->logs("Created new dataset detail ID: {$detail->id}");
            }

            DB::commit();

            session()->flash('success', $this->editMode ? 'Dataset detail updated successfully.' : 'Dataset created successfully.');
            $this->showModal = false;
            $this->reset(['level_id', 'type_id', 'dimension', 'indicator_id', 'yearData', 'valueId', 'editMode']);
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save error: ' . $e->getMessage());
            $this->addError('save', 'Something went wrong while saving: ' . $e->getMessage());
        }
    }

    // Delete Dataset
    public function confirmDelete($id)
    {
        $this->valueId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->valueId) {
            $dataset = PydpDatasetDetail::findOrFail($this->valueId);
            $dataset->delete();

            $this->logs("Deleted dataset detail: {$this->valueId}");

            session()->flash('success', 'Dataset detail deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'valueId']);
    }

    public function exportDatasetDetails()
    {
        $yearStart = $this->datasetInfo['type']->year_start;
        $yearEnd = $this->datasetInfo['type']->year_end;

        $yearRange = range($yearStart, $yearEnd);

        return Excel::download(new DatasetDetailExport($yearRange, $this->datasetInfo['id']), 'dataset_details.xlsx');
    }

    public function logs($action)
    {
        UserLog::create([
            'user_id' => auth()->id(),
            'action'  => $action,
        ]);
    }

    public function render()
    {
        $query = PydpDatasetDetail::with(['indicator.level', 'pydpDataset', 'dimension'])
            ->where('pydp_dataset_id', $this->datasetInfo['id'])
            ->when($this->search, function ($q) {
                $q->whereHas('indicator', function ($q2) {
                    $q2->where('title', 'like', '%' . $this->search . '%');
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.user.pydp-dataset-detail-index', compact('tableDatas'));
    }
}
