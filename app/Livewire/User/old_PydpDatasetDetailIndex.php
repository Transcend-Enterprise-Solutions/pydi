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
        // Ensure the dataset belongs to the current user or is accessible to them
        $dataset = PydpDataset::with(['type'])->findOrFail($id);

        // Add additional check if datasets have user ownership
        // Uncomment if datasets should be user-specific:
        // if ($dataset->user_id !== auth()->id()) {
        //     abort(403, 'Access denied to this dataset.');
        // }

        $this->datasetInfo = $dataset;

        // Get only levels that belong to the current user
        $this->levels = PydpLevel::where('user_id', auth()->id())
                                ->where('is_active', 1)
                                ->get();

        $this->types = PydpType::all();

        // Get only indicators that belong to levels owned by the current user
        $userLevelIds = collect($this->levels)->pluck('id');
        $this->allIndicators = PydpIndicator::whereIn('pydp_level_id', $userLevelIds)->get();

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
            // Only get indicators for levels owned by the current user
            return PydpIndicator::where('pydp_level_id', $this->level_id)
                               ->whereHas('level', function($query) {
                                   $query->where('user_id', auth()->id());
                               })
                               ->get();
        }

        return $this->allIndicators;
    }

    // Update indicators when level changes
    public function updatedLevelId($value)
    {
        // Reset indicator when level changes
        $this->indicator_id = null;

        if ($value) {
            // Verify the selected level belongs to the current user
            $levelBelongsToUser = PydpLevel::where('id', $value)
                                          ->where('user_id', auth()->id())
                                          ->exists();

            if (!$levelBelongsToUser) {
                $this->addError('level_id', 'Selected level is not accessible.');
                return;
            }

            // Get indicators for the selected level that belongs to the current user
            $this->indicators = PydpIndicator::where('pydp_level_id', $value)
                                            ->whereHas('level', function($query) {
                                                $query->where('user_id', auth()->id());
                                            })
                                            ->get();
        } else {
            // Show all indicators for user's levels
            $userLevelIds = collect($this->levels)->pluck('id');
            $this->indicators = PydpIndicator::whereIn('pydp_level_id', $userLevelIds)->get();
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
        // Ensure the dataset detail is accessible (through indicator ownership)
        $data = PydpDatasetDetail::with(['years', 'indicator.level'])
                                 ->whereHas('indicator.level', function($query) {
                                     $query->where('user_id', auth()->id());
                                 })
                                 ->where('id', $id)
                                 ->first();

        if (!$data) {
            session()->flash('error', 'Dataset detail not found or access denied.');
            return;
        }

        $this->valueId = $data->id;
        $this->level_id = $data->indicator->pydp_level_id ?? null;
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

        // Additional validation to ensure selected level belongs to current user
        if ($this->level_id) {
            $levelExists = PydpLevel::where('id', $this->level_id)
                                   ->where('user_id', auth()->id())
                                   ->exists();
            if (!$levelExists) {
                $this->addError('level_id', 'Selected level is not accessible.');
                return;
            }
        }

        // Additional validation to ensure selected indicator belongs to user's level
        if ($this->indicator_id) {
            $indicatorExists = PydpIndicator::where('id', $this->indicator_id)
                                           ->whereHas('level', function($query) {
                                               $query->where('user_id', auth()->id());
                                           })
                                           ->exists();
            if (!$indicatorExists) {
                $this->addError('indicator_id', 'Selected indicator is not accessible.');
                return;
            }
        }

        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->editMode) {
                // Verify ownership before updating
                $existingDetail = PydpDatasetDetail::whereHas('indicator.level', function($query) {
                    $query->where('user_id', auth()->id());
                })->where('id', $this->valueId)->first();

                if (!$existingDetail) {
                    DB::rollBack();
                    $this->addError('save', 'Access denied to update this dataset detail.');
                    return;
                }

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
        // Verify the dataset detail is accessible before allowing delete
        $detailExists = PydpDatasetDetail::whereHas('indicator.level', function($query) {
            $query->where('user_id', auth()->id());
        })->where('id', $id)->exists();

        if (!$detailExists) {
            session()->flash('error', 'Dataset detail not found or access denied.');
            return;
        }

        $this->valueId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->valueId) {
            // Ensure the dataset detail belongs to user's indicators
            $dataset = PydpDatasetDetail::whereHas('indicator.level', function($query) {
                $query->where('user_id', auth()->id());
            })->where('id', $this->valueId)->first();

            if (!$dataset) {
                session()->flash('error', 'Dataset detail not found or access denied.');
                $this->reset(['showDeleteModal', 'valueId']);
                return;
            }

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

        return Excel::download(new DatasetDetailExport($yearRange, $this->datasetInfo['id'], auth()->id()), 'dataset_details.xlsx');
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
            ->whereHas('indicator.level', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($this->search, function ($q) {
                $q->whereHas('indicator', function ($q2) {
                    $q2->where('title', 'like', '%' . $this->search . '%');
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.user.pydp-dataset-detail-index', compact('tableDatas'));
    }
}
