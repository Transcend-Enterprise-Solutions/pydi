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
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
#[Title('Data Entry | PYDI')]
class DataEntryBatchManager extends Component
{
    public $dimensions;
    public $indicators = [];
    public $currentSession = null;
    public $sessionRecords = [];

    // Session creation
    public $showSessionModal = false;
    public $sessionName = '';
    public $sessionDescription = '';
    public $selectedDimensionId = null;
    public $selectedIndicatorId = null;

    // Data entry
    public $showDataEntryModal = false;
    public $region = '';
    public $sex = '';
    public $age = '';
    public $value = '';
    public $editingRecordId = null;

    // Options
    public $regions = [];
    public $sexOptions = ['Male', 'Female', 'Both'];
    public $ageGroups = [
        '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39',
        '40-44', '45-49', '50-54', '55-59', '60-64', '65-69', '70-74', '75+'
    ];

    public function mount()
    {
        $this->regions = PhilippineRegions::orderBy('region_description')->pluck('region_description', 'region_code')->toArray();
        $this->loadDimensions();
        $this->loadCurrentSession();
    }

    public function loadDimensions()
    {
        $this->dimensions = Dimension::with('indicators')->orderBy('name')->get();
    }

    public function loadCurrentSession()
    {
        $this->currentSession = UploadSession::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->with(['dimension', 'indicator', 'dataRecords'])
            ->first();

        if ($this->currentSession) {
            $this->sessionRecords = $this->currentSession->dataRecords->toArray();
            $this->selectedDimensionId = $this->currentSession->dimension_id;
            $this->selectedIndicatorId = $this->currentSession->indicator_id;
            $this->loadIndicators();
        }
    }

    public function updatedSelectedDimensionId()
    {
        $this->loadIndicators();
        $this->selectedIndicatorId = null;
    }

    public function loadIndicators()
    {
        if ($this->selectedDimensionId) {
            $this->indicators = Indicator::where('dimension_id', $this->selectedDimensionId)
                ->orderBy('name')
                ->get();
        } else {
            $this->indicators = [];
        }
    }

    public function openSessionModal()
    {
        $this->resetSessionForm();
        $this->showSessionModal = true;
    }

    public function closeSessionModal()
    {
        $this->showSessionModal = false;
        $this->resetSessionForm();
    }

    public function createSession()
    {
        $this->validate([
            'sessionName' => 'required|min:3|max:255',
            'sessionDescription' => 'nullable|max:500',
            'selectedDimensionId' => 'required|exists:dimensions,id',
            'selectedIndicatorId' => 'required|exists:indicators,id'
        ]);

        try {
            // Check if user has existing draft session
            $existingSession = UploadSession::where('user_id', Auth::id())
                ->where('status', 'draft')
                ->first();

            if ($existingSession) {
                $this->dispatch('swal', [
                    'title' => 'Warning!',
                    'text' => 'You have an existing draft session. Please complete or cancel it first.',
                    'icon' => 'warning'
                ]);
                return;
            }

            $this->currentSession = UploadSession::create([
                'user_id' => Auth::id(),
                'dimension_id' => $this->selectedDimensionId,
                'indicator_id' => $this->selectedIndicatorId,
                'session_name' => $this->sessionName,
                'notes' => $this->sessionDescription,
                'status' => 'draft'
            ]);

            $this->sessionRecords = [];
            $this->closeSessionModal();
            $this->loadIndicators();

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Session created successfully. You can now add data records.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to create session: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function openDataEntryModal($recordId = null)
    {
        if (!$this->currentSession) {
            $this->dispatch('swal', [
                'title' => 'No Session Selected',
                'text' => 'Please create a session first before adding data records.',
                'icon' => 'warning'
            ]);
            return;
        }

        if ($recordId) {
            $record = collect($this->sessionRecords)->firstWhere('id', $recordId);
            if ($record) {
                $this->editingRecordId = $recordId;
                $this->region = $record['region'];
                $this->sex = $record['sex'];
                $this->age = $record['age'];
                $this->value = $record['value'];
            }
        } else {
            $this->resetDataEntryForm();
        }

        $this->showDataEntryModal = true;
    }

    public function closeDataEntryModal()
    {
        $this->showDataEntryModal = false;
        $this->resetDataEntryForm();
    }

    public function saveDataRecord()
    {
        $this->validate([
            'region' => 'required',
            'sex' => 'required',
            'age' => 'required',
            'value' => 'required|numeric'
        ]);

        try {
            if ($this->editingRecordId) {
                // Update existing record
                $record = PydiDataRecord::find($this->editingRecordId);
                $record->update([
                    'region' => $this->region,
                    'sex' => $this->sex,
                    'age' => $this->age,
                    'value' => $this->value
                ]);
                $message = 'Data record updated successfully';
            } else {
                // Create new record
                $record = PydiDataRecord::create([
                    'upload_session_id' => $this->currentSession->id,
                    'dimension_id' => $this->currentSession->dimension_id,
                    'indicator_id' => $this->currentSession->indicator_id,
                    'user_id' => Auth::id(),
                    'region' => $this->region,
                    'sex' => $this->sex,
                    'age' => $this->age,
                    'value' => $this->value,
                    'status' => 'draft'
                ]);
                $message = 'Data record added successfully';
            }

            $this->loadCurrentSession(); // Refresh session records
            $this->closeDataEntryModal();

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => $message,
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to save data record: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function deleteDataRecord($recordId)
    {
        try {
            PydiDataRecord::find($recordId)->delete();
            $this->loadCurrentSession();

            $this->dispatch('swal', [
                'title' => 'Deleted!',
                'text' => 'Data record deleted successfully.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to delete record: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function submitSession()
    {
        if (!$this->currentSession || empty($this->sessionRecords)) {
            $this->dispatch('swal', [
                'title' => 'Cannot Submit',
                'text' => 'Please add at least one data record before submitting.',
                'icon' => 'warning'
            ]);
            return;
        }

        try {
            DB::transaction(function () {
                // Update session status
                $this->currentSession->update([
                    'status' => 'submitted',
                    'submitted_at' => now()
                ]);

                // Update all records in the session
                PydiDataRecord::where('upload_session_id', $this->currentSession->id)
                    ->update(['status' => 'submitted', 'submitted_at' => now()]);
            });

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Session submitted successfully!',
                'icon' => 'success'
            ]);

            // Reset current session
            $this->currentSession = null;
            $this->sessionRecords = [];
            $this->selectedDimensionId = null;
            $this->selectedIndicatorId = null;
            $this->indicators = [];

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to submit session: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function cancelSession()
    {
        if ($this->currentSession) {
            try {
                // Delete all records and the session
                PydiDataRecord::where('upload_session_id', $this->currentSession->id)->delete();
                $this->currentSession->delete();

                $this->currentSession = null;
                $this->sessionRecords = [];
                $this->selectedDimensionId = null;
                $this->selectedIndicatorId = null;
                $this->indicators = [];

                $this->dispatch('swal', [
                    'title' => 'Cancelled!',
                    'text' => 'Session cancelled successfully.',
                    'icon' => 'success'
                ]);
            } catch (\Exception $e) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Failed to cancel session: ' . $e->getMessage(),
                    'icon' => 'error'
                ]);
            }
        }
    }

    public function exportToCsv(): StreamedResponse
    {
        if (!$this->currentSession || empty($this->sessionRecords)) {
            $this->dispatch('swal', [
                'title' => 'Cannot Export',
                'text' => 'No data available to export.',
                'icon' => 'warning'
            ]);
            throw new \Exception('No data available to export');
        }

        $fileName = 'pydi-data-' . $this->currentSession->id . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Dimension',
                'Indicator',
                'Region',
                'Sex',
                'Age Group',
                'Value',
                'Status',
                'Created At'
            ]);

            // Data
            foreach ($this->sessionRecords as $record) {
                fputcsv($file, [
                    $this->getDimensionName($record['dimension_id']),
                    $this->getIndicatorName($record['indicator_id']),
                    $record['region'],
                    $record['sex'],
                    $record['age'],
                    $this->formatValue($record['value'], $record['indicator_id']),
                    $record['status'] ?? 'draft',
                    $record['created_at'] ?? now()->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        }, $fileName);
    }

    public function duplicateSession()
    {
        if (!$this->currentSession) {
            $this->dispatch('swal', [
                'title' => 'No Session',
                'text' => 'No session to duplicate.',
                'icon' => 'warning'
            ]);
            return;
        }

        try {
            DB::transaction(function () {
                // Create new session
                $newSession = $this->currentSession->replicate();
                $newSession->session_name = $this->currentSession->session_name . ' (Copy)';
                $newSession->status = 'draft';
                $newSession->submitted_at = null;
                $newSession->save();

                // Duplicate all records
                foreach ($this->currentSession->dataRecords as $record) {
                    $newRecord = $record->replicate();
                    $newRecord->upload_session_id = $newSession->id;
                    $newRecord->status = 'draft';
                    $newRecord->submitted_at = null;
                    $newRecord->save();
                }

                $this->currentSession = $newSession;
                $this->loadCurrentSession();
            });

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Session duplicated successfully.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to duplicate session: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function resetSessionForm()
    {
        $this->sessionName = '';
        $this->sessionDescription = '';
        $this->selectedDimensionId = null;
        $this->selectedIndicatorId = null;
        $this->indicators = [];
    }

    public function resetDataEntryForm()
    {
        $this->region = '';
        $this->sex = '';
        $this->age = '';
        $this->value = '';
        $this->editingRecordId = null;
    }

    protected function getDimensionName($dimensionId): string
    {
        return Dimension::find($dimensionId)?->name ?? 'N/A';
    }

    protected function getIndicatorName($indicatorId): string
    {
        return Indicator::find($indicatorId)?->name ?? 'N/A';
    }

    protected function formatValue($value, $indicatorId = null): string
    {
        $unit = $indicatorId
            ? Indicator::find($indicatorId)?->measurement_unit
            : $this->currentSession?->indicator?->measurement_unit;

        return number_format($value, 2) . ($unit ? ' ' . $unit : '');
    }

    public function render()
    {
        return view('livewire.user.data-entry-batch-manager', [
            'dimensionName' => $this->currentSession?->dimension?->name ?? 'N/A',
            'indicatorName' => $this->currentSession?->indicator?->name ?? 'N/A',
            'measurementUnit' => $this->currentSession?->indicator?->measurement_unit ?? '',
            'recordsCount' => count($this->sessionRecords),
            'sessionStatus' => $this->currentSession?->status ?? 'none',
            'sessionCreatedAt' => $this->currentSession?->created_at?->format('M d, Y h:i A') ?? 'N/A',
            'sessionSubmittedAt' => $this->currentSession?->submitted_at?->format('M d, Y h:i A') ?? 'Not submitted',
        ]);
    }
}
