<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\PydpLevel;
use App\Models\PydpIndicator;
use App\Models\PydpDatasetEntry;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PydpDataMultiSheetExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use ZipArchive;

#[Layout('layouts.app')]
class PydpManagement extends Component
{
    use WithPagination;

    public $pageTitle = 'PYDP Management';
    public $pageDescription = 'Review submissions and edit requests';
    public $pageIcon = '📋';

    // ============ FILTERS ============
    public $filterStatus = 'submitted'; // submitted, approved, rejected, edit-requests
    public $filterAgency = '';
    public $filterUser = '';
    public $searchLevel = '';
    public $perPage = 25;

    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showApproveEditModal = false;
    public $showRejectEditModal = false;
    
    public $expandedLevels = [];
    public $expandedIndicators = [];

    public $selectedEntryId = '';
    public $selectedIndicatorId = '';
    public $approvalComments = '';
    public $rejectionReason = '';
    public $approveEditNotes = '';
    public $rejectEditNotes = '';

    public $selectedUsers = [];
    public $selectedLevels = [];
    public $agencies = [];
    public $users = [];

    public $statsPending = 0;
    public $statsApproved = 0;
    public $statsRejected = 0;
    public $statsEditRequests = 0;
    public $statsTotal = 0;

    public function mount()
    {
        $this->loadStats();
        $this->loadAgencies();
        $this->loadUsers();
    }

    public function loadStats()
    {
        $this->statsPending = PydpDatasetEntry::where('submission_status', 'submitted')
            ->where('edit_requested', false)
            ->count();
        $this->statsApproved = PydpDatasetEntry::where('submission_status', 'approved')->count();
        $this->statsRejected = PydpDatasetEntry::where('submission_status', 'rejected')->count();
        $this->statsEditRequests = PydpDatasetEntry::where('edit_requested', true)
            ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
            ->count();
        $this->statsTotal = PydpDatasetEntry::count();
    }

    public function loadAgencies()
    {
        $this->agencies = \App\Models\UserData::distinct()
                                              ->whereNotNull('government_agency')
                                              ->pluck('government_agency')
                                              ->sort()
                                              ->values();
    }

    public function loadUsers()
    {
        $this->users = User::where('user_role', 'user')
                           ->with(['userData', 'pydpLevels'])
                           ->orderBy('id')
                           ->get();
    }

    // ============ UNIFIED QUERY ============

    public function getLevelsOrIndicators()
    {
        if ($this->filterStatus === 'edit-requests') {
            // Show indicators with edit requests
            return $this->getEditRequestIndicators();
        } else {
            // Show levels with submissions
            return $this->getLevelsWithSubmissions();
        }
    }

    private function getLevelsWithSubmissions()
    {
        $query = PydpLevel::with([
            'user.userData',
            'indicators' => function ($q) {
                $q->with(['entries' => function ($e) {
                    $e->where('submission_status', $this->filterStatus)
                      ->where('edit_requested', false)
                      ->orderBy('year', 'desc');
                }])
                ->orderBy('title'); // Sort indicators by title
            }
        ]);

        if ($this->searchLevel) {
            $query->where('title', 'like', '%' . $this->searchLevel . '%');
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterAgency) {
            $query->whereHas('user.userData', function ($q) {
                $q->where('government_agency', $this->filterAgency);
            });
        }

        $query->whereHas('indicators.entries', function ($q) {
            $q->where('submission_status', $this->filterStatus)
              ->where('edit_requested', false);
        });

        // Sort by level title
        $query->orderBy('title');

        return $query->paginate($this->perPage);
    }

    private function getEditRequestIndicators()
    {
        $query = PydpIndicator::with([
            'level' => function ($q) {
                $q->with('user.userData');
            },
            'entries' => function ($q) {
                $q->where('edit_requested', true)
                  ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
                  ->orderBy('year', 'desc');
            }
        ]);

        if ($this->searchLevel) {
            $query->whereHas('level', function ($q) {
                $q->where('title', 'like', '%' . $this->searchLevel . '%');
            });
        }

        if ($this->filterUser) {
            $query->whereHas('level', function ($q) {
                $q->where('user_id', $this->filterUser);
            });
        }

        if ($this->filterAgency) {
            $query->whereHas('level.user.userData', function ($q) {
                $q->where('government_agency', $this->filterAgency);
            });
        }

        $query->whereHas('entries', function ($q) {
            $q->where('edit_requested', true)
              ->whereIn('submission_status', ['submitted', 'approved', 'rejected']);
        });

        // Sort by level title, then indicator title
        $query->join('pydp_levels', 'pydp_indicators.pydp_level_id', '=', 'pydp_levels.id')
              ->orderBy('pydp_levels.title')
              ->orderBy('pydp_indicators.title')
              ->select('pydp_indicators.*');

        return $query->paginate($this->perPage);
    }

    // ============ TOGGLE METHODS ============

    public function toggleLevel($levelId)
    {
        if (in_array($levelId, $this->expandedLevels)) {
            $this->expandedLevels = array_values(
                array_filter($this->expandedLevels, fn($id) => $id !== $levelId)
            );
        } else {
            $this->expandedLevels[] = $levelId;
        }
    }

    public function toggleIndicator($indicatorId)
    {
        if (in_array($indicatorId, $this->expandedIndicators)) {
            $this->expandedIndicators = array_values(
                array_filter($this->expandedIndicators, fn($id) => $id !== $indicatorId)
            );
        } else {
            $this->expandedIndicators[] = $indicatorId;
        }
    }

    // ============ APPROVE/REJECT SUBMISSIONS ============

    public function openApproveModal($entryId)
    {
        $this->selectedEntryId = $entryId;
        $this->approvalComments = '';
        $this->showApproveModal = true;
    }

    public function approveSubmission()
    {
        try {
            $entry = PydpDatasetEntry::find($this->selectedEntryId);
            
            if (!$entry) {
                throw new \Exception('Submission not found!');
            }

            $entry->update([
                'submission_status' => 'approved',
                'admin_notes' => $this->approvalComments,
            ]);

            Log::channel('pydp_actions')->info('Entry approved by admin', [
                'entry_id' => $entry->id,
                'admin_id' => auth()->id(),
                'comments' => $this->approvalComments,
            ]);

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Submission approved successfully!',
                'icon' => 'success'
            ]);

            $this->closeModals();
            $this->loadStats();
        } catch (\Exception $e) {
            Log::error('Error approving submission: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function openRejectModal($entryId)
    {
        $this->selectedEntryId = $entryId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function rejectSubmission()
    {
        try {
            if (!$this->rejectionReason) {
                throw new \Exception('Please provide a rejection reason!');
            }

            $entry = PydpDatasetEntry::find($this->selectedEntryId);
            
            if (!$entry) {
                throw new \Exception('Submission not found!');
            }

            $entry->update([
                'submission_status' => 'rejected',
                'admin_notes' => $this->rejectionReason,
            ]);

            Log::channel('pydp_actions')->info('Entry rejected by admin', [
                'entry_id' => $entry->id,
                'admin_id' => auth()->id(),
                'reason' => $this->rejectionReason,
            ]);

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Submission rejected successfully!',
                'icon' => 'success'
            ]);

            $this->closeModals();
            $this->loadStats();
        } catch (\Exception $e) {
            Log::error('Error rejecting submission: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ APPROVE/REJECT EDIT REQUESTS ============

    public function openApproveEditModal($indicatorId)
    {
        $this->selectedIndicatorId = $indicatorId;
        $this->approveEditNotes = '';
        $this->showApproveEditModal = true;
    }

    public function approveEditRequest()
    {
        try {
            $indicator = PydpIndicator::find($this->selectedIndicatorId);
            
            if (!$indicator) {
                throw new \Exception('Indicator not found!');
            }

            $entries = $indicator->entries()
                ->where('edit_requested', true)
                ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
                ->get();

            if ($entries->isEmpty()) {
                throw new \Exception('No edit requests found for this indicator!');
            }

            // Update only columns that exist in the database
            $indicator->entries()
                ->where('edit_requested', true)
                ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
                ->update([
                    'submission_status' => 'draft',
                    'edit_requested' => false,
                    'edit_request_reason' => null,
                    'edit_requested_at' => null,
                    'admin_notes' => $this->approveEditNotes,
                ]);

            Log::channel('pydp_actions')->info('Edit request approved by admin', [
                'indicator_id' => $this->selectedIndicatorId,
                'indicator_title' => $indicator->title,
                'admin_id' => auth()->id(),
                'entries_count' => $entries->count(),
                'notes' => $this->approveEditNotes,
            ]);

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => "Edit request approved! User can now edit {$entries->count()} entry/entries.",
                'icon' => 'success'
            ]);

            $this->closeModals();
            $this->loadStats();
        } catch (\Exception $e) {
            Log::error('Error approving edit request: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function openRejectEditModal($indicatorId)
    {
        $this->selectedIndicatorId = $indicatorId;
        $this->rejectEditNotes = '';
        $this->showRejectEditModal = true;
    }

    public function rejectEditRequest()
    {
        try {
            if (!$this->rejectEditNotes) {
                throw new \Exception('Please provide a reason for rejection!');
            }

            $indicator = PydpIndicator::find($this->selectedIndicatorId);
            
            if (!$indicator) {
                throw new \Exception('Indicator not found!');
            }

            $entries = $indicator->entries()
                ->where('edit_requested', true)
                ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
                ->get();

            if ($entries->isEmpty()) {
                throw new \Exception('No edit requests found for this indicator!');
            }

            // Update only columns that exist in the database
            $indicator->entries()
                ->where('edit_requested', true)
                ->whereIn('submission_status', ['submitted', 'approved', 'rejected'])
                ->update([
                    'edit_requested' => false,
                    'edit_request_reason' => null,
                    'edit_requested_at' => null,
                    'admin_notes' => $this->rejectEditNotes,
                ]);

            Log::channel('pydp_actions')->info('Edit request rejected by admin', [
                'indicator_id' => $this->selectedIndicatorId,
                'indicator_title' => $indicator->title,
                'admin_id' => auth()->id(),
                'entries_count' => $entries->count(),
                'reason' => $this->rejectEditNotes,
            ]);

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Edit request rejected!',
                'icon' => 'success'
            ]);

            $this->closeModals();
            $this->loadStats();
        } catch (\Exception $e) {
            Log::error('Error rejecting edit request: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ REPORT GENERATION ============

    public function toggleCheckAll()
    {
        if ($this->filterStatus === 'edit-requests') {
            // For edit requests, we work with indicators
            $allIndicatorIds = $this->getEditRequestIndicators()->pluck('id')->toArray();
            
            if (count($this->selectedLevels) === count($allIndicatorIds)) {
                $this->selectedLevels = [];
            } else {
                $this->selectedLevels = $allIndicatorIds;
            }
        } else {
            // For submissions, we work with levels
            $allLevelIds = $this->getLevelsWithSubmissions()->pluck('id')->toArray();
            
            if (count($this->selectedLevels) === count($allLevelIds)) {
                $this->selectedLevels = [];
            } else {
                $this->selectedLevels = $allLevelIds;
            }
        }
    }

    public function generateReport()
    {
        try {
            if (empty($this->selectedLevels)) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Please select at least one level to generate the report!',
                    'icon' => 'error'
                ]);
                return;
            }

            $selectedLevelsCopy = $this->selectedLevels;
            
            if (count($selectedLevelsCopy) === 1) {
                // Single level export
                $levelId = reset($selectedLevelsCopy);
                $level = PydpLevel::with('user.userData')->find($levelId);
                
                if (!$level) {
                    $this->dispatch('swal', [
                        'title' => 'Warning!',
                        'text' => 'Selected level not found.',
                        'icon' => 'warning'
                    ]);
                    return;
                }

                $userId = $level->user_id;
                $userName = $level->user->userData?->first_name ?? $level->user->name ?? 'User';
                $levelTitle = str_replace(' ', '_', $level->title);
                $fileName = 'PYDP_' . $levelTitle . '_' . $userName . '_' . now()->format('Y_m_d_His') . '.xlsx';

                Log::channel('pydp_actions')->info('Admin generated single-level report', [
                    'admin_id' => auth()->id(),
                    'level_id' => $levelId,
                    'level_title' => $level->title,
                ]);

                $this->selectedLevels = [];

                return Excel::download(
                    new PydpDataMultiSheetExport($userId, [$levelId]),
                    $fileName
                );
            }

            // Multiple levels export - create ZIP
            $zipFileName = 'PYDP_Admin_Report_' . now()->format('Y_m_d_His') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);
            
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file');
            }

            foreach ($selectedLevelsCopy as $levelId) {
                $level = PydpLevel::with('user.userData')->find($levelId);
                
                if (!$level) {
                    continue;
                }

                $userId = $level->user_id;
                $userName = $level->user->userData?->first_name ?? $level->user->name ?? 'User';
                $levelTitle = str_replace(' ', '_', $level->title);
                $fileName = 'PYDP_' . $levelTitle . '_' . $userName . '_' . now()->format('Y_m_d_His') . '.xlsx';

                $tempFilePath = storage_path('app/temp/' . $fileName);
                Excel::store(
                    new PydpDataMultiSheetExport($userId, [$levelId]),
                    'temp/' . $fileName
                );

                $zip->addFile($tempFilePath, $fileName);
            }

            $zip->close();

            Log::channel('pydp_actions')->info('Admin generated multi-level report', [
                'admin_id' => auth()->id(),
                'levels_count' => count($selectedLevelsCopy),
            ]);

            $this->selectedLevels = [];

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to generate report: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    // ============ UTILITIES ============

    public function closeModals()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->showApproveEditModal = false;
        $this->showRejectEditModal = false;
        $this->approvalComments = '';
        $this->rejectionReason = '';
        $this->approveEditNotes = '';
        $this->rejectEditNotes = '';
        $this->selectedEntryId = '';
        $this->selectedIndicatorId = '';
    }

    public function resetFilters()
    {
        $this->filterStatus = 'submitted';
        $this->filterAgency = '';
        $this->filterUser = '';
        $this->searchLevel = '';
        $this->expandedLevels = [];
        $this->expandedIndicators = [];
        $this->resetPage();
    }

    public function render()
    {
        $data = $this->getLevelsOrIndicators();
        
        return view('livewire.admin.pydp-management', [
            'items' => $data,
            'isEditRequestView' => $this->filterStatus === 'edit-requests',
            'pageTitle' => $this->pageTitle,
            'pageDescription' => $this->pageDescription,
            'pageIcon' => $this->pageIcon,
            'expandedLevels' => $this->expandedLevels,
            'expandedIndicators' => $this->expandedIndicators,
            'agencies' => $this->agencies,
            'users' => $this->users,
        ]);
    }
}