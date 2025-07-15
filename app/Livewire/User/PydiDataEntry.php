<?php

namespace App\Livewire\User;

use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\PhilippineRegions;
use App\Models\PydiDataRecord;
use App\Models\UploadSession;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Data Entry | PYDI')]
class PydiDataEntry extends Component
{
    // Session properties
    public $currentSession = null;
    public $sessionName = '';
    public $sessionNotes = '';
    public $showCreateSession = false;


    // Form properties for data entry
    public $rows = [];
    public $newRow = [
        'dimension_id' => '',
        'indicator_id' => '',
        'region' => '',
        'sex' => '',
        'age' => '',
        'value' => '',
        'remarks' => '',
    ];

    // Dropdown data
    public $dimensions = [];
    public $indicators = [];
    public $regions = [];
    public $sexOptions = ['Male', 'Female', 'Both'];
    public $ageGroups = [
        '0-14', '15-24', '25-34', '35-44', '45-54', '55-64', '65+', 'All Ages'
    ];

    // UI state
    public $showAddRow = false;
    public $editingRowIndex = null;

    public $showViewSubmitted = false;
    public $submittedSessions = [];
    public $selectedSubmittedSession = null;
    public $submittedSessionData = [];

    protected $rules = [
        'sessionName' => 'required|string|max:255',
        'sessionNotes' => 'nullable|string',
        'newRow.dimension_id' => 'required|exists:dimensions,id',
        'newRow.indicator_id' => 'required|exists:indicators,id',
        'newRow.region' => 'required|string',
        'newRow.sex' => 'required|string',
        'newRow.age' => 'required|string',
        'newRow.value' => 'required|numeric|min:0',
        'newRow.remarks' => 'nullable|string',
    ];

    protected $messages = [
        'newRow.dimension_id.required' => 'Please select a dimension.',
        'newRow.indicator_id.required' => 'Please select an indicator.',
        'newRow.region.required' => 'Please select a region.',
        'newRow.sex.required' => 'Please select a sex.',
        'newRow.age.required' => 'Please select an age group.',
        'newRow.value.required' => 'Please enter a value.',
        'newRow.value.numeric' => 'Value must be a number.',
        'newRow.value.min' => 'Value must be greater than or equal to 0.',
    ];

    public function mount()
    {
        $this->loadDropdownData();
        $this->loadActiveSession();
    }
//view functions
    public function showSubmittedDatasets()
    {

        $this->submittedSessions = UploadSession::where('user_id', Auth::id())
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $this->showViewSubmitted = true;
    }

    public function loadSubmittedSessionData($sessionId)
    {
        $this->selectedSubmittedSession = UploadSession::find($sessionId);

        $this->submittedSessionData = PydiDataRecord::where('upload_session_id', $sessionId)
            ->with(['dimension', 'indicator'])
            ->get()
            ->map(function ($record) {
                return [
                    'dimension_name' => $record->dimension->name,
                    'indicator_name' => $record->indicator->name,
                    'region' => $this->getRegionName($record->region),
                    'sex' => $record->sex,
                    'age' => $record->age,
                    'value' => number_format($record->value, 4),
                    'remarks' => $record->remarks ?: '-',
                ];
            })->toArray();
    }



    public function loadDropdownData()
    {
        $this->dimensions = Dimension::orderBy('name')->get();
        $this->indicators = Indicator::orderBy('name')->get();
        $this->regions = PhilippineRegions::orderBy('region_description')->get();
    }

    public function loadActiveSession()
    {
        $this->currentSession = UploadSession::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($this->currentSession) {
            $this->loadSessionData();
        }
    }

    public function loadSessionData()
    {
        if ($this->currentSession) {
            $this->rows = $this->currentSession->draftRecords()
                ->with(['dimension', 'indicator'])
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'dimension_id' => $record->dimension_id,
                        'indicator_id' => $record->indicator_id,
                        'region' => $record->region,
                        'sex' => $record->sex,
                        'age' => $record->age,
                        'value' => $record->value,
                        'remarks' => $record->remarks,
                        'dimension_name' => $record->dimension->name,
                        'indicator_name' => $record->indicator->name,
                    ];
                })->toArray();
        }
    }

    public function showCreateSessionForm()
    {
        $this->showCreateSession = true;
        $this->sessionName = 'Dataset ' . now()->format('Y-m-d H:i:s');
    }

    public function createSession()
    {
        $this->validate([
            'sessionName' => 'required|string|max:255',
            'sessionNotes' => 'nullable|string',
        ]);

        DB::transaction(function () {
            $this->currentSession = UploadSession::create([
                'user_id' => Auth::id(),
                'session_name' => $this->sessionName,
                'status' => 'active',
                'notes' => $this->sessionNotes,
                'total_records' => 0,
            ]);
        });

        $this->showCreateSession = false;
        $this->sessionName = '';
        $this->sessionNotes = '';
        $this->rows = [];

        session()->flash('success', 'New dataset created successfully!');
    }

    public function cancelCreateSession()
    {
        $this->showCreateSession = false;
        $this->sessionName = '';
        $this->sessionNotes = '';
    }

    public function showAddRowForm()
    {
        $this->showAddRow = true;
        $this->resetNewRow();
    }

    public function resetNewRow()
    {
        $this->newRow = [
            'dimension_id' => '',
            'indicator_id' => '',
            'region' => '',
            'sex' => '',
            'age' => '',
            'value' => '',
            'remarks' => '',
        ];
    }

    public function addRow()
    {
        if (!$this->currentSession) {
            session()->flash('error', 'Please create a dataset first.');
            return;
        }

        $this->validate([
            'newRow.dimension_id' => 'required|exists:dimensions,id',
            'newRow.indicator_id' => 'required|exists:indicators,id',
            'newRow.region' => 'required|string',
            'newRow.sex' => 'required|string',
            'newRow.age' => 'required|string',
            'newRow.value' => 'required|numeric|min:0',
            'newRow.remarks' => 'nullable|string',
        ]);

        DB::transaction(function () {
            $record = PydiDataRecord::create([
                'upload_session_id' => $this->currentSession->id,
                'dimension_id' => $this->newRow['dimension_id'],
                'indicator_id' => $this->newRow['indicator_id'],
                'user_id' => Auth::id(),
                'region' => $this->newRow['region'],
                'sex' => $this->newRow['sex'],
                'age' => $this->newRow['age'],
                'value' => $this->newRow['value'],
                'remarks' => $this->newRow['remarks'],
                'status' => 'draft',
            ]);

            $this->currentSession->updateRecordsCount();
        });

        $this->loadSessionData();
        $this->showAddRow = false;
        $this->resetNewRow();

        session()->flash('success', 'Row added successfully!');
    }

    public function editRow($index)
    {
        $row = $this->rows[$index];
        $this->newRow = [
            'dimension_id' => $row['dimension_id'],
            'indicator_id' => $row['indicator_id'],
            'region' => $row['region'],
            'sex' => $row['sex'],
            'age' => $row['age'],
            'value' => $row['value'],
            'remarks' => $row['remarks'],
        ];
        $this->editingRowIndex = $index;
        $this->showAddRow = true;
    }

    public function updateRow()
    {
        if (!$this->currentSession || $this->editingRowIndex === null) {
            return;
        }

        $this->validate([
            'newRow.dimension_id' => 'required|exists:dimensions,id',
            'newRow.indicator_id' => 'required|exists:indicators,id',
            'newRow.region' => 'required|string',
            'newRow.sex' => 'required|string',
            'newRow.age' => 'required|string',
            'newRow.value' => 'required|numeric|min:0',
            'newRow.remarks' => 'nullable|string',
        ]);

        $row = $this->rows[$this->editingRowIndex];

        DB::transaction(function () use ($row) {
            PydiDataRecord::where('id', $row['id'])->update([
                'dimension_id' => $this->newRow['dimension_id'],
                'indicator_id' => $this->newRow['indicator_id'],
                'region' => $this->newRow['region'],
                'sex' => $this->newRow['sex'],
                'age' => $this->newRow['age'],
                'value' => $this->newRow['value'],
                'remarks' => $this->newRow['remarks'],
            ]);
        });

        $this->loadSessionData();
        $this->showAddRow = false;
        $this->editingRowIndex = null;
        $this->resetNewRow();

        session()->flash('success', 'Row updated successfully!');
    }

    public function deleteRow($index)
    {
        if (!$this->currentSession) {
            return;
        }

        $row = $this->rows[$index];

        DB::transaction(function () use ($row) {
            PydiDataRecord::where('id', $row['id'])->delete();
            $this->currentSession->updateRecordsCount();
        });

        $this->loadSessionData();
        session()->flash('success', 'Row deleted successfully!');
    }

    public function cancelAddRow()
    {
        $this->showAddRow = false;
        $this->editingRowIndex = null;
        $this->resetNewRow();
    }

    public function submitForReview()
    {
        if (!$this->currentSession || empty($this->rows)) {
            session()->flash('error', 'Please add at least one row before submitting.');
            return;
        }

        DB::transaction(function () {
            // Update all draft records to submitted
            PydiDataRecord::where('upload_session_id', $this->currentSession->id)
                ->where('status', 'draft')
                ->update([
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

            // Mark session as submitted
            $this->currentSession->markAsSubmitted();
        });

        $this->currentSession = null;
        $this->rows = [];

        session()->flash('success', 'Dataset submitted for review successfully!');
    }

    public function cancelSession()
    {
        if (!$this->currentSession) {
            return;
        }

        DB::transaction(function () {
            // Delete all draft records
            PydiDataRecord::where('upload_session_id', $this->currentSession->id)
                ->where('status', 'draft')
                ->delete();

            // Mark session as cancelled
            $this->currentSession->markAsCancelled();
        });

        $this->currentSession = null;
        $this->rows = [];

        session()->flash('success', 'Dataset cancelled successfully!');
    }

    public function getRegionName($regionCode)
    {
        $region = collect($this->regions)->firstWhere('region_code', $regionCode);
        return $region ? $region->region_description : $regionCode;
    }

    public function render()
    {
        return view('livewire.user.pydi-data-entry');
    }
}
