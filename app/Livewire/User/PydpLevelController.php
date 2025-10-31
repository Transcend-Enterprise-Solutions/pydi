<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\PydpLevel;
use App\Models\PydpIndicator;
use App\Models\PydpDatasetEntry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PydpDataMultiSheetExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class PydpLevelController extends Component
{
    // ============ DISPLAY STATE ============
    public $dimensions;
    public $showDimensionModal = false;
    public $showIndicatorModal = false;
    public $showDeleteModal = false;
    public $showSubmitModal = false;
    public $showEditRequestModal = false;
    public $showReportModal = false;
    public $expandedIndicators = [];
    public $editModes = [];
    public $selectedLevels = [];
    
    // ============ FORM DATA - DIMENSION/LEVEL ============
    public $dimensionName = '';
    public $dimensionDescription = '';
    public $editingDimensionId = null;
    
    // ============ FORM DATA - INDICATOR ============
    public $indicatorName = '';
    public $indicatorDescription = '';
    public $indicatorDataSources = '';
    public $indicatorFrequency = '';
    public $indicatorResponsible = '';
    public $indicatorValidation = '';
    public $indicatorDataSharing = '';
    public $indicatorMeasurementUnit = '';
    public $editingIndicatorId = null;
    public $selectedDimensionId = null;
    
    // ============ FORM DATA - DELETE ============
    public $type = '';
    public $deleteId = null;
    
    // ============ FORM DATA - SUBMISSION/EDIT ============
    public $submissionNotes = '';
    public $editRequestReason = '';
    public $currentLevelId = null;

    // ============ LIFECYCLE ============

    public function mount()
    {
        $this->loadDimensions();
    }

    public function render()
    {
        return view('livewire.user.pydp-level-controller', [
            'dimensions' => $this->dimensions,
        ]);
    }

    public function loadDimensions()
    {
        $this->dimensions = PydpLevel::with(['indicators.entries'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ============ DIMENSION (LEVEL) METHODS ============

    public function openDimensionModal($id = null)
    {
        if ($id) {
            $dimension = PydpLevel::where('user_id', auth()->id())->find($id);
            
            if (!$dimension) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Level not found.',
                    'icon' => 'error'
                ]);
                return;
            }

            $this->editingDimensionId = $id;
            $this->dimensionName = $dimension->title;
            $this->dimensionDescription = $dimension->content;
        } else {
            $this->editingDimensionId = null;
            $this->dimensionName = '';
            $this->dimensionDescription = '';
        }
        
        $this->showDimensionModal = true;
    }

    public function closeDimensionModal()
    {
        $this->showDimensionModal = false;
        $this->editingDimensionId = null;
        $this->dimensionName = '';
        $this->dimensionDescription = '';
    }

    public function saveDimension()
    {
        $this->validate([
            'dimensionName' => 'required|string|max:255',
            'dimensionDescription' => 'nullable|string',
        ], [
            'dimensionName.required' => 'Level title is required.',
        ]);

        try {
            if ($this->editingDimensionId) {
                // Update existing
                $dimension = PydpLevel::where('user_id', auth()->id())
                    ->find($this->editingDimensionId);
                
                if (!$dimension) {
                    throw new \Exception('Level not found.');
                }

                $dimension->update([
                    'title' => $this->dimensionName,
                    'content' => $this->dimensionDescription,
                ]);

                $this->logAction('Updated level: ' . $this->dimensionName);
                $this->dispatch('swal', [
                    'title' => 'Success!',
                    'text' => 'Level updated successfully.',
                    'icon' => 'success'
                ]);
            } else {
                // Create new
                PydpLevel::create([
                    'user_id' => auth()->id(),
                    'title' => $this->dimensionName,
                    'content' => $this->dimensionDescription,
                ]);

                $this->logAction('Created new level: ' . $this->dimensionName);
                $this->dispatch('swal', [
                    'title' => 'Success!',
                    'text' => 'Level created successfully.',
                    'icon' => 'success'
                ]);
            }

            $this->loadDimensions();
            $this->closeDimensionModal();
        } catch (\Exception $e) {
            Log::error('Error saving dimension: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ INDICATOR METHODS ============

    public function openIndicatorModal($dimensionId, $indicatorId = null)
    {
        $dimension = PydpLevel::where('user_id', auth()->id())->find($dimensionId);
        
        if (!$dimension) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Level not found.',
                'icon' => 'error'
            ]);
            return;
        }

        $this->selectedDimensionId = $dimensionId;

        if ($indicatorId) {
            $indicator = PydpIndicator::where('pydp_level_id', $dimensionId)->find($indicatorId);
            
            if (!$indicator) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Indicator not found.',
                    'icon' => 'error'
                ]);
                return;
            }

            $this->editingIndicatorId = $indicatorId;
            $this->indicatorName = $indicator->title;
            $this->indicatorDescription = $indicator->content;
            $this->indicatorDataSources = $indicator->data_sources;
            $this->indicatorFrequency = $indicator->frequency;
            $this->indicatorResponsible = $indicator->responsible;
            $this->indicatorValidation = $indicator->validation;
            $this->indicatorDataSharing = $indicator->data_sharing;
            $this->indicatorMeasurementUnit = $indicator->measurement_unit;
        } else {
            $this->editingIndicatorId = null;
            $this->resetIndicatorForm();
        }
        
        $this->showIndicatorModal = true;
    }

    public function closeIndicatorModal()
    {
        $this->showIndicatorModal = false;
        $this->editingIndicatorId = null;
        $this->selectedDimensionId = null;
        $this->resetIndicatorForm();
    }

    public function saveIndicator()
    {
        $this->validate([
            'indicatorName' => 'required|string|max:255',
            'indicatorDescription' => 'nullable|string',
            'indicatorDataSources' => 'nullable|string',
            'indicatorFrequency' => 'nullable|string',
            'indicatorResponsible' => 'nullable|string',
            'indicatorValidation' => 'nullable|string',
            'indicatorDataSharing' => 'nullable|string',
            'indicatorMeasurementUnit' => 'nullable|string',
        ], [
            'indicatorName.required' => 'Indicator name is required.',
        ]);

        try {
            if ($this->editingIndicatorId) {
                // Update existing
                $indicator = PydpIndicator::find($this->editingIndicatorId);
                
                if (!$indicator) {
                    throw new \Exception('Indicator not found.');
                }

                $indicator->update([
                    'title' => $this->indicatorName,
                    'content' => $this->indicatorDescription,
                    'data_sources' => $this->indicatorDataSources,
                    'frequency' => $this->indicatorFrequency,
                    'responsible' => $this->indicatorResponsible,
                    'validation' => $this->indicatorValidation,
                    'data_sharing' => $this->indicatorDataSharing,
                    'measurement_unit' => $this->indicatorMeasurementUnit,
                ]);

                $this->logAction('Updated indicator: ' . $this->indicatorName);
                $this->dispatch('swal', [
                    'title' => 'Success!',
                    'text' => 'Indicator updated successfully.',
                    'icon' => 'success'
                ]);
            } else {
                // Create new
                $indicator = PydpIndicator::create([
                    'pydp_level_id' => $this->selectedDimensionId,
                    'title' => $this->indicatorName,
                    'content' => $this->indicatorDescription,
                    'data_sources' => $this->indicatorDataSources,
                    'frequency' => $this->indicatorFrequency,
                    'responsible' => $this->indicatorResponsible,
                    'validation' => $this->indicatorValidation,
                    'data_sharing' => $this->indicatorDataSharing,
                    'measurement_unit' => $this->indicatorMeasurementUnit,
                ]);

                // Create entries for 2023-2028
                for ($year = 2023; $year <= 2028; $year++) {
                    PydpDatasetEntry::create([
                        'pydp_indicator_id' => $indicator->id,
                        'year' => $year,
                        'submission_status' => 'draft',
                        'edit_requested' => false,
                    ]);
                }

                $this->logAction('Created new indicator: ' . $this->indicatorName);
                $this->dispatch('swal', [
                    'title' => 'Success!',
                    'text' => 'Indicator created with data entries for 2023-2028.',
                    'icon' => 'success'
                ]);
            }

            $this->loadDimensions();
            $this->closeIndicatorModal();
        } catch (\Exception $e) {
            Log::error('Error saving indicator: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function resetIndicatorForm()
    {
        $this->indicatorName = '';
        $this->indicatorDescription = '';
        $this->indicatorDataSources = '';
        $this->indicatorFrequency = '';
        $this->indicatorResponsible = '';
        $this->indicatorValidation = '';
        $this->indicatorDataSharing = '';
        $this->indicatorMeasurementUnit = '';
    }

    // ============ SUBMISSION - PER LEVEL ============

    public function openSubmitModal($dimensionId)
    {
        $level = PydpLevel::where('user_id', auth()->id())->find($dimensionId);
        
        if (!$level) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Level not found.',
                'icon' => 'error'
            ]);
            return;
        }

        $this->currentLevelId = $dimensionId;
        $this->submissionNotes = '';
        $this->showSubmitModal = true;
    }

    public function submitLevelWithNotes()
    {
        try {
            $level = PydpLevel::where('user_id', auth()->id())
                ->find($this->currentLevelId);
            
            if (!$level) {
                throw new \Exception('Level not found.');
            }

            // Update all indicators in this level to submitted
            $indicators = $level->indicators;
            
            if ($indicators->isEmpty()) {
                throw new \Exception('This level has no indicators to submit.');
            }

            foreach ($indicators as $indicator) {
                $indicator->entries()->update([
                    'submission_status' => 'submitted',
                    'submission_notes' => $this->submissionNotes,
                    'submitted_at' => now(),
                ]);
            }

            $this->logAction("Submitted level: {$level->title} ({$indicators->count()} indicators)");
            
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => "Level '{$level->title}' and all {$indicators->count()} indicator(s) submitted successfully.",
                'icon' => 'success'
            ]);

            $this->showSubmitModal = false;
            $this->submissionNotes = '';
            $this->loadDimensions();
        } catch (\Exception $e) {
            Log::error('Error submitting level: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ EDIT REQUEST - PER LEVEL ============

    public function openEditRequestModal($dimensionId)
    {
        $level = PydpLevel::where('user_id', auth()->id())->find($dimensionId);
        
        if (!$level) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Level not found.',
                'icon' => 'error'
            ]);
            return;
        }

        $this->currentLevelId = $dimensionId;
        $this->editRequestReason = '';
        $this->showEditRequestModal = true;
    }

    public function requestEditAccessLevel()
    {
        $this->validate([
            'editRequestReason' => 'required|string|min:10',
        ], [
            'editRequestReason.required' => 'Please provide a reason for the edit request.',
            'editRequestReason.min' => 'Reason must be at least 10 characters.',
        ]);

        try {
            $level = PydpLevel::where('user_id', auth()->id())
                ->find($this->currentLevelId);
            
            if (!$level) {
                throw new \Exception('Level not found.');
            }

            // Update all indicators in this level with edit request
            $indicators = $level->indicators;
            
            if ($indicators->isEmpty()) {
                throw new \Exception('This level has no indicators to edit.');
            }

            foreach ($indicators as $indicator) {
                $indicator->entries()->update([
                    'edit_requested' => true,
                    'edit_request_reason' => $this->editRequestReason,
                    'edit_requested_at' => now(),
                ]);
            }

            $this->logAction("Requested edit for level: {$level->title}");
            
            $this->dispatch('swal', [
                'title' => 'Request Sent!',
                'text' => "Edit request for '{$level->title}' sent to admin. Pending approval.",
                'icon' => 'success'
            ]);

            $this->showEditRequestModal = false;
            $this->editRequestReason = '';
            $this->loadDimensions();
        } catch (\Exception $e) {
            Log::error('Error requesting edit: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ DETAILS TOGGLE ============

    public function toggleIndicatorDetails($indicatorId)
    {
        if (in_array($indicatorId, $this->expandedIndicators)) {
            $this->expandedIndicators = array_values(
                array_filter($this->expandedIndicators, fn($id) => $id !== $indicatorId)
            );
        } else {
            $this->expandedIndicators[] = $indicatorId;
        }
    }

    public function toggleEditMode($indicatorId)
    {
        $indicator = PydpIndicator::find($indicatorId);
        
        if (!$indicator) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Indicator not found.',
                'icon' => 'error'
            ]);
            return;
        }

        if (isset($this->editModes[$indicatorId]) && $this->editModes[$indicatorId]) {
            $this->editModes[$indicatorId] = false;
        } else {
            $this->editModes[$indicatorId] = true;
        }
    }

    // ============ DATA ENTRIES ============

    public function saveEntry($indicatorId, $year, $field, $value)
    {
        try {
            $indicator = PydpIndicator::find($indicatorId);
            
            if (!$indicator) {
                throw new \Exception('Indicator not found.');
            }

            // Find or create entry
            $entry = PydpDatasetEntry::where('pydp_indicator_id', $indicatorId)
                ->where('year', $year)
                ->first();

            if (!$entry) {
                // Create entry if it doesn't exist
                $entry = PydpDatasetEntry::create([
                    'pydp_indicator_id' => $indicatorId,
                    'year' => $year,
                    'submission_status' => 'draft',
                    'edit_requested' => false,
                ]);
            }

            // Get old value for logging
            $oldValue = $entry->$field ?? null;
            
            // Update the field
            $entry->$field = $value;
            $entry->save();

            // Log significant changes
            if ($oldValue !== $value && !is_null($value)) {
                $this->logAction("Updated {$field} for indicator '{$indicator->title}' (Year: {$year}) from '{$oldValue}' to '{$value}'");
            }

            Log::debug("Entry saved successfully", [
                'indicator_id' => $indicatorId,
                'year' => $year,
                'field' => $field,
                'value' => $value
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving entry: ' . $e->getMessage(), [
                'indicator_id' => $indicatorId,
                'year' => $year,
                'field' => $field,
                'value' => $value,
                'exception' => $e
            ]);
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to save entry: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ DELETE ============

    public function confirmAction($id, $type)
    {
        if ($type === 'deleteDimension') {
            $dimension = PydpLevel::where('user_id', auth()->id())->find($id);
            if (!$dimension) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Level not found.',
                    'icon' => 'error'
                ]);
                return;
            }

            // Check if any indicator in this level is submitted/approved
            $lockedIndicators = $dimension->indicators()
                ->whereHas('entries', function ($query) {
                    $query->whereIn('submission_status', ['submitted', 'approved']);
                })
                ->count();

            if ($lockedIndicators > 0) {
                $this->dispatch('swal', [
                    'title' => 'Cannot Delete!',
                    'text' => 'Cannot delete level with submitted or approved indicators. Please contact admin.',
                    'icon' => 'warning'
                ]);
                return;
            }
        } else {
            $indicator = PydpIndicator::find($id);
            if (!$indicator) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Indicator not found.',
                    'icon' => 'error'
                ]);
                return;
            }

            // Check if indicator is submitted/approved
            $status = $indicator->entries()->first()?->submission_status ?? 'draft';
            if (in_array($status, ['submitted', 'approved'])) {
                $this->dispatch('swal', [
                    'title' => 'Cannot Delete!',
                    'text' => 'Cannot delete indicator with submitted or approved status. Please contact admin.',
                    'icon' => 'warning'
                ]);
                return;
            }
        }

        $this->deleteId = $id;
        $this->type = $type === 'deleteDimension' ? 'Level' : 'Indicator';
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->type === 'Level') {
                $level = PydpLevel::where('user_id', auth()->id())
                    ->find($this->deleteId);
                
                if (!$level) {
                    throw new \Exception('Level not found.');
                }

                $levelTitle = $level->title;
                $indicatorCount = $level->indicators->count();
                
                $level->delete();
                
                $this->logAction("Deleted level: {$levelTitle} (with {$indicatorCount} indicators)");
                
                $this->dispatch('swal', [
                    'title' => 'Deleted!',
                    'text' => "Level '{$levelTitle}' and all its indicators have been deleted.",
                    'icon' => 'success'
                ]);
            } else {
                $indicator = PydpIndicator::find($this->deleteId);
                
                if (!$indicator) {
                    throw new \Exception('Indicator not found.');
                }

                $indicatorTitle = $indicator->title;
                
                $indicator->delete();
                
                $this->logAction("Deleted indicator: {$indicatorTitle}");
                
                $this->dispatch('swal', [
                    'title' => 'Deleted!',
                    'text' => "Indicator '{$indicatorTitle}' has been deleted.",
                    'icon' => 'success'
                ]);
            }

            $this->showDeleteModal = false;
            $this->loadDimensions();
        } catch (\Exception $e) {
            Log::error('Error deleting: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ REPORT GENERATION ============

    public function openReportModal()
    {
        $this->selectedLevels = [];
        $this->showReportModal = true;
    }

    public function generateReport()
    {
        try {
            if (empty($this->selectedLevels)) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Please select at least one level to generate the report.',
                    'icon' => 'error'
                ]);
                return;
            }

            // Verify levels belong to user
            $levelsCount = PydpLevel::whereIn('id', $this->selectedLevels)
                ->where('user_id', auth()->id())
                ->count();

            if ($levelsCount !== count($this->selectedLevels)) {
                throw new \Exception('Invalid level selection.');
            }

            // Check if selected levels have indicators
            $hasIndicators = PydpIndicator::whereIn('pydp_level_id', $this->selectedLevels)
                ->count();

            if ($hasIndicators === 0) {
                $this->dispatch('swal', [
                    'title' => 'No Data!',
                    'text' => 'Selected levels have no indicators with data.',
                    'icon' => 'info'
                ]);
                return;
            }

            $user = auth()->user();
            $fileName = 'PYDP_Report_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Y_m_d_His') . '.xlsx';
            
            $this->logAction("Generated Excel report: {$fileName} with " . count($this->selectedLevels) . " level(s)");

            $this->showReportModal = false;
            $selectedLevelsCopy = $this->selectedLevels;
            $this->selectedLevels = [];

            return Excel::download(
                new PydpDataMultiSheetExport(auth()->id(), $selectedLevelsCopy),
                $fileName
            );
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to generate report: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ HELPERS ============

    public function logAction($message)
    {
        try {
            Log::channel('pydp_actions')->info($message, [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging action: ' . $e->getMessage());
        }
    }

    // ============ REFRESH METHODS ============

    public function refresh()
    {
        $this->loadDimensions();
    }

    public function resetAllState()
    {
        $this->showDimensionModal = false;
        $this->showIndicatorModal = false;
        $this->showDeleteModal = false;
        $this->showSubmitModal = false;
        $this->showEditRequestModal = false;
        $this->showReportModal = false;
        $this->expandedIndicators = [];
        $this->editModes = [];
        $this->selectedLevels = [];
        $this->editingDimensionId = null;
        $this->editingIndicatorId = null;
        $this->selectedDimensionId = null;
        $this->currentLevelId = null;
        $this->resetIndicatorForm();
    }
}