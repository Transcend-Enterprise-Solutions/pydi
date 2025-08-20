<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Illuminate\Support\Facades\DB;
use Livewire\{WithPagination, WithFileUploads};
use App\Models\{PydpDatasetDetail, PydpDataset, Dimension, PydpYear, UserLog};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatasetDetailExport;

#[Layout('layouts.app')]
#[Title('PYDP Dataset Detail')]
class PydpDatasetDetailIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $datasetInfo = [], $indicators = [], $dimensions = [], $yearRange = [];
    public $showEntries = 10;
    public $search = '';

    public $dimension, $indicator_id;
    public $yearData = [];
    public $valueId;
    public $remarks;
    public $total;
    public $baseline;

    public $showDeleteModal = false;
    public $showModal = false;
    public $editMode = false;

    public function mount($id)
    {
        $dataset = PydpDataset::with(['type', 'level.indicators'])->findOrFail($id);
        $this->datasetInfo = $dataset;

        $this->indicators = $dataset->level->indicators;
        $this->dimensions = Dimension::all();

        $years = range((int)$dataset->type->year_start, (int)$dataset->type->year_end);
        $this->yearRange = array_map('strval', $years);
    }

    protected $rules = [
        'dimension' => 'required|exists:dimensions,id',
        'indicator_id' => 'required|exists:pydp_indicators,id',
        'baseline' => 'required|integer|min:0',
        'remarks' => 'nullable|string|max:1000',
        'yearData' => 'required|array',
        'yearData.*.physical_target' => 'nullable|numeric',
        'yearData.*.financial_target' => 'nullable|numeric',
        'yearData.*.physical_actual' => 'nullable|numeric',
        'yearData.*.financial_actual' => 'nullable|numeric',
    ];

    public function create()
    {
        $this->reset(['dimension', 'indicator_id', 'yearData', 'remarks', 'valueId']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $data = PydpDatasetDetail::with('years')->findOrFail($id);

        $this->valueId = $data->id;
        $this->dimension = $data->dimension_id;
        $this->indicator_id = $data->pydp_indicator_id;
        $this->baseline = $data->baseline;
        $this->total = $data->total;
        $this->remarks = $data->remarks;

        // Populate year data
        $this->yearData = [];
        foreach ($data->years as $year) {
            $this->yearData[$year->year] = [
                'physical_target' => $year->target_physical,
                'financial_target' => $year->target_financial,
                'physical_actual' => $year->actual_physical,
                'financial_actual' => $year->actual_financial,
            ];
        }

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->editMode) {
                // Update dataset detail
                PydpDatasetDetail::where('id', $this->valueId)->update([
                    'pydp_dataset_id' => $this->datasetInfo['id'],
                    'dimension_id' => $this->dimension,
                    'pydp_indicator_id' => $this->indicator_id,
                    'baseline' => $this->baseline,
                    'total' => $this->total,
                    'remarks' => $this->remarks,
                ]);

                // Delete old years and reinsert
                PydpYear::where('pydp_dataset_detail_id', $this->valueId)->delete();

                foreach ($this->yearData as $year => $values) {
                    PydpYear::create([
                        'pydp_dataset_detail_id' => $this->valueId,
                        'year' => $year,
                        'target_physical' => $values['physical_target'] ?? 0,
                        'target_financial' => $values['financial_target'] ?? 0,
                        'actual_physical' => $values['physical_actual'] ?? 0,
                        'actual_financial' => $values['financial_actual'] ?? 0,
                    ]);
                }

                $this->logs("Updated dataset detail ID: {$this->valueId}");
            } else {
                // Create new detail
                $detail = PydpDatasetDetail::create([
                    'pydp_dataset_id' => $this->datasetInfo['id'],
                    'dimension_id' => $this->dimension,
                    'pydp_indicator_id' => $this->indicator_id,
                    'baseline' => $this->baseline,
                    'total' => $this->total,
                    'remarks' => $this->remarks,
                ]);

                foreach ($this->yearData as $year => $values) {
                    PydpYear::create([
                        'pydp_dataset_detail_id' => $detail->id,
                        'year' => $year,
                        'target_physical' => $values['physical_target'] ?? 0,
                        'target_financial' => $values['financial_target'] ?? 0,
                        'actual_physical' => $values['physical_actual'] ?? 0,
                        'actual_financial' => $values['financial_actual'] ?? 0,
                    ]);
                }

                $this->logs("Created new dataset detail ID: {$detail->id}");
            }

            DB::commit();

            session()->flash('success', $this->editMode ? 'Dataset detail updated successfully.' : 'Dataset created successfully.');
            $this->showModal = false;
            $this->reset(['dimension', 'indicator_id', 'yearData', 'remarks', 'valueId', 'editMode']);
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'Something went wrong while saving. Please try again.');
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
        $query = PydpDatasetDetail::with(['indicator', 'pydpDataset', 'dimension'])
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
