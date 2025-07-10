<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Dimension;
use App\Models\Indicator;
use App\Models\UploadSession;
use App\Models\PydiDataRecord;
use App\Models\PhilippineRegions;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PydiDataImport;

#[Layout('layouts.app')]
#[Title('Data Entry | PYDI')]
class PydiDataEntry extends Component
{
    use WithFileUploads;

    // Tab management
    public $activeTab = 'upload';

    // Manual entry form
    public $sessionName = '';
    public $dimensionId = '';
    public $indicatorId = '';
    public $region = '';
    public $sex = '';
    public $age = '';
    public $value = '';
    public $remarks = '';

    // File upload
    public $uploadFile;
    public $datasetFile; // Added missing property
    public $uploadSessionName = '';
    public $uploadDimensionId = '';
    public $uploadIndicatorId = '';
    public $uploadMethod = 'manual';

    // Data
    public $dimensions = [];
    public $indicators = [];
    public $regions = [];
    public $activeSessions = [];
    public $allSessions = []; // Added missing property
    public $selectedSessionRecords = [];
    public $selectedSession = null; // Added missing property

    // UI States
    public $showAddForm = false;
    public $showSessionDetails = false;
    public $showSessionModal = false; // Added missing property
    public $selectedSessionId = null;
    public $isProcessing = false;

    // Sex options
    public $sexOptions = [
        'Male' => 'Male',
        'Female' => 'Female',
        'Both' => 'Both Sexes'
    ];

    // Age group options
    public $ageOptions = [
        '0-4' => '0-4 years',
        '5-9' => '5-9 years',
        '10-14' => '10-14 years',
        '15-19' => '15-19 years',
        '20-24' => '20-24 years',
        '25-29' => '25-29 years',
        '30-34' => '30-34 years',
        '35-39' => '35-39 years',
        '40-44' => '40-44 years',
        '45-49' => '45-49 years',
        '50-54' => '50-54 years',
        '55-59' => '55-59 years',
        '60-64' => '60-64 years',
        '65+' => '65+ years',
        'All' => 'All Ages'
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->dimensions = Dimension::orderBy('name')->get();
        $this->regions = PhilippineRegions::orderBy('region_description')->get();
        $this->loadActiveSessions();
        $this->loadAllSessions();
    }

    public function loadActiveSessions()
    {
        $this->activeSessions = UploadSession::forUser(Auth::id())
            ->active()
            ->with(['dataRecords'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Added missing method
    public function loadAllSessions()
    {
        $this->allSessions = UploadSession::forUser(Auth::id())
            ->with(['dataRecords'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Added missing method
    public function setUploadMethod($method)
    {
        $this->uploadMethod = $method;
        $this->resetForm();
    }

    // Added missing method
    public function loadIndicators()
    {
        $this->indicatorId = '';
        $this->indicators = [];

        if ($this->dimensionId) {
            $this->indicators = Indicator::where('dimension_id', $this->dimensionId)
                ->orderBy('name')
                ->get();
        }
    }

    public function updatedDimensionId($value)
    {
        $this->indicatorId = '';
        $this->indicators = [];

        if ($value) {
            $this->indicators = Indicator::where('dimension_id', $value)
                ->orderBy('name')
                ->get();
        }
    }

    public function updatedUploadDimensionId($value)
    {
        $this->uploadIndicatorId = '';

        if ($value) {
            $this->indicators = Indicator::where('dimension_id', $value)
                ->orderBy('name')
                ->get();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetForm();
    }

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
        if (!$this->showAddForm) {
            $this->resetManualForm();
        }
    }

    public function resetForm()
    {
        $this->resetManualForm();
        $this->resetUploadForm();
        $this->showAddForm = false;
        $this->showSessionDetails = false;
        $this->showSessionModal = false;
    }

    public function resetManualForm()
    {
        $this->sessionName = '';
        $this->dimensionId = '';
        $this->indicatorId = '';
        $this->region = '';
        $this->sex = '';
        $this->age = '';
        $this->value = '';
        $this->remarks = '';
        $this->indicators = [];
    }

    public function resetUploadForm()
    {
        $this->uploadFile = null;
        $this->datasetFile = null;
        $this->uploadSessionName = '';
        $this->uploadDimensionId = '';
        $this->uploadIndicatorId = '';
    }

    // Updated method name to match template
    public function addRecord()
    {
        $this->validate([
            'sessionName' => 'required|string|max:255',
            'dimensionId' => 'required|exists:dimensions,id',
            'indicatorId' => 'required|exists:indicators,id',
            'region' => 'required|string',
            'sex' => 'required|in:Male,Female,Both',
            'age' => 'required|string',
            'value' => 'required|numeric',
            'remarks' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create or get existing upload session
            $session = UploadSession::firstOrCreate([
                'user_id' => Auth::id(),
                'session_name' => $this->sessionName,
                'status' => 'active',
            ]);

            // Create the data record
            PydiDataRecord::create([
                'upload_session_id' => $session->id,
                'dimension_id' => $this->dimensionId,
                'indicator_id' => $this->indicatorId,
                'user_id' => Auth::id(),
                'region' => $this->region,
                'sex' => $this->sex,
                'age' => $this->age,
                'value' => $this->value,
                'remarks' => $this->remarks,
                'status' => 'draft',
            ]);

            // Update session record count
            $session->updateRecordsCount();

            DB::commit();

            $this->resetManualForm();
            $this->loadActiveSessions();
            $this->loadAllSessions();
            $this->showAddForm = false;

            session()->flash('message', 'Data record added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to add data record: ' . $e->getMessage());
        }
    }

    // Updated method name to match template
    public function uploadFile()
    {
        $this->validate([
            'sessionName' => 'required|string|max:255',
            'dimensionId' => 'required|exists:dimensions,id',
            'indicatorId' => 'required|exists:indicators,id',
            'datasetFile' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        $this->isProcessing = true;

        DB::beginTransaction();
        try {
            // Create upload session
            $session = UploadSession::create([
                'user_id' => Auth::id(),
                'session_name' => $this->sessionName,
                'status' => 'active',
                'notes' => 'File upload: ' . $this->datasetFile->getClientOriginalName(),
            ]);

            // Process the file
            $import = new PydiDataImport(
                $session->id,
                $this->dimensionId,
                $this->indicatorId,
                Auth::id()
            );

            Excel::import($import, $this->datasetFile->getRealPath());

            // Update session record count
            $session->updateRecordsCount();

            DB::commit();

            $this->resetUploadForm();
            $this->loadActiveSessions();
            $this->loadAllSessions();
            $this->isProcessing = false;

            session()->flash('message', 'File uploaded and processed successfully! ' . $import->getImportedCount() . ' records imported.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->isProcessing = false;
            session()->flash('error', 'Failed to process file: ' . $e->getMessage());
        }
    }

    // Updated method name to match template
    public function viewSession($sessionId)
    {
        $this->selectedSessionId = $sessionId;
        $this->selectedSession = UploadSession::with(['dataRecords.dimension', 'dataRecords.indicator'])
            ->where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if ($this->selectedSession) {
            $this->selectedSessionRecords = $this->selectedSession->dataRecords;
            $this->showSessionModal = true;
        }
    }

    // Added missing method
    public function closeSessionModal()
    {
        $this->showSessionModal = false;
        $this->selectedSessionId = null;
        $this->selectedSession = null;
        $this->selectedSessionRecords = [];
    }

    public function viewSessionDetails($sessionId)
    {
        $this->selectedSessionId = $sessionId;
        $this->selectedSessionRecords = PydiDataRecord::where('upload_session_id', $sessionId)
            ->with(['dimension', 'indicator'])
            ->orderBy('created_at', 'desc')
            ->get();
        $this->showSessionDetails = true;
    }

    public function closeSessionDetails()
    {
        $this->showSessionDetails = false;
        $this->selectedSessionId = null;
        $this->selectedSessionRecords = [];
    }

    public function submitSession($sessionId)
    {
        $session = UploadSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$session->isActive()) {
            session()->flash('error', 'Session cannot be submitted.');
            return;
        }

        DB::beginTransaction();
        try {
            // Mark session as submitted
            $session->markAsSubmitted();

            // Mark all draft records in this session as submitted
            $session->draftRecords()->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            DB::commit();

            $this->loadActiveSessions();
            $this->loadAllSessions();
            $this->closeSessionDetails();
            $this->closeSessionModal();

            session()->flash('message', 'Session submitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to submit session: ' . $e->getMessage());
        }
    }

    public function cancelSession($sessionId)
    {
        $session = UploadSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$session->isActive()) {
            session()->flash('error', 'Session cannot be cancelled.');
            return;
        }

        DB::beginTransaction();
        try {
            // Mark session as cancelled
            $session->markAsCancelled();

            // Delete all draft records in this session
            $session->draftRecords()->delete();

            DB::commit();

            $this->loadActiveSessions();
            $this->loadAllSessions();
            $this->closeSessionDetails();
            $this->closeSessionModal();

            session()->flash('message', 'Session cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Failed to cancel session: ' . $e->getMessage());
        }
    }

    public function deleteRecord($recordId)
    {
        $record = PydiDataRecord::where('id', $recordId)
            ->where('user_id', Auth::id())
            ->where('status', 'draft')
            ->firstOrFail();

        try {
            $sessionId = $record->upload_session_id;
            $record->delete();

            // Update session record count
            $session = UploadSession::find($sessionId);
            if ($session) {
                $session->updateRecordsCount();

                // If no records left, delete the session
                if ($session->dataRecords()->count() === 0) {
                    $session->delete();
                }
            }

            $this->loadActiveSessions();
            $this->loadAllSessions();

            // Refresh session details if viewing
            if ($this->selectedSessionId == $sessionId) {
                $this->viewSessionDetails($sessionId);
            }

            session()->flash('message', 'Record deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pydi_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Region', 'Sex', 'Age', 'Value', 'Remarks']);

            // Add sample data
            fputcsv($file, ['NCR', 'Male', '0-4', '1234.56', 'Sample data']);
            fputcsv($file, ['NCR', 'Female', '5-9', '2345.67', 'Sample data']);
            fputcsv($file, ['Region I', 'Both', 'All', '3456.78', 'Sample data']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.user.pydi-data-entry');
    }
}
