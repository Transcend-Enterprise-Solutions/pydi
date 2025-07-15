<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Accomplishments;
use App\Models\Indicator;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('View Datasets | PYDI')]
class ViewDatasets extends Component
{
    public $showReviewModal = false;
    public $selectedAccomplishment = null;
    public $reviewData = [];
    public $feedback = '';
    public $statusFilter = 'all';
    public $searchTerm = '';

    public $availableYears = [
        '2024', '2025', '2026', '2027', '2028'
    ];

    public function mount()
    {
        $this->initializeReviewData();
    }

    public function initializeReviewData()
    {
        foreach ($this->availableYears as $year) {
            $this->reviewData[$year] = [
                'status' => 'pending',
                'feedback' => ''
            ];
        }
    }

    public function openReviewModal($ppaName, $indicatorId)
    {
        $this->selectedAccomplishment = [
            'ppa_name' => $ppaName,
            'indicator_id' => $indicatorId
        ];
        
        // Load existing data
        $accomplishments = Accomplishments::where('ppa_name', $ppaName)
            ->where('indicator_id', $indicatorId)
            ->with('indicator')
            ->get();
            
        foreach ($accomplishments as $accomplishment) {
            $this->reviewData[$accomplishment->year] = [
                'status' => $accomplishment->status,
                'feedback' => $accomplishment->admin_feedback ?? ''
            ];
        }
        
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->selectedAccomplishment = null;
        $this->initializeReviewData();
    }

    public function submitReview()
    {
        $this->validate([
            'reviewData.*.status' => 'required|in:pending,approved,rejected,needs_revision'
        ]);

        if (!$this->selectedAccomplishment) {
            return;
        }

        $accomplishments = Accomplishments::where('ppa_name', $this->selectedAccomplishment['ppa_name'])
            ->where('indicator_id', $this->selectedAccomplishment['indicator_id'])
            ->get();

        foreach ($accomplishments as $accomplishment) {
            $yearData = $this->reviewData[$accomplishment->year];
            
            $accomplishment->update([
                'status' => $yearData['status'],
                'admin_feedback' => $yearData['feedback'] ?: null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);
        }

        $this->dispatch('swal', [
            'title' => 'Success',
            'text' => 'Review submitted successfully!',
            'icon' => 'success',
        ]);

        $this->closeReviewModal();
    }

    public function bulkApprove($ppaName, $indicatorId)
    {
        Accomplishments::where('ppa_name', $ppaName)
            ->where('indicator_id', $indicatorId)
            ->update([
                'status' => 'approved',
                'admin_feedback' => null,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

        $this->dispatch('swal', [
            'title' => 'Success',
            'text' => 'All years approved successfully!',
            'icon' => 'success',
        ]);
    }

    public function render()
    {
        $accomplishments = $this->getAccomplishmentsForReview();
        
        return view('livewire.admin.view-datasets', [
            'accomplishments' => $accomplishments
        ]);
    }

    public function getAccomplishmentsForReview()
    {
        $query = Accomplishments::with(['indicator.dimension', 'reviewer'])
            ->orderBy('ppa_name')
            ->orderBy('indicator_id')
            ->orderBy('year');

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('ppa_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('indicator', function($indicator) {
                      $indicator->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        $accomplishments = $query->get();

        $groupedData = [];
        
        foreach ($accomplishments as $accomplishment) {
            $key = $accomplishment->ppa_name . '_' . $accomplishment->indicator_id;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'ppa_name' => $accomplishment->ppa_name,
                    'indicator' => $accomplishment->indicator,
                    'years' => [],
                    'pending_count' => 0,
                    'approved_count' => 0,
                    'rejected_count' => 0,
                    'needs_revision_count' => 0,
                    'last_reviewed' => null
                ];
            }
            
            $groupedData[$key]['years'][$accomplishment->year] = $accomplishment;
            
            // Count statuses
            switch ($accomplishment->status) {
                case 'pending':
                    $groupedData[$key]['pending_count']++;
                    break;
                case 'approved':
                    $groupedData[$key]['approved_count']++;
                    break;
                case 'rejected':
                    $groupedData[$key]['rejected_count']++;
                    break;
                case 'needs_revision':
                    $groupedData[$key]['needs_revision_count']++;
                    break;
            }
            
            // Track last reviewed date
            if ($accomplishment->reviewed_at && 
                (!$groupedData[$key]['last_reviewed'] || 
                 $accomplishment->reviewed_at > $groupedData[$key]['last_reviewed'])) {
                $groupedData[$key]['last_reviewed'] = $accomplishment->reviewed_at;
            }
        }
        
        return $groupedData;
    }

    public function updatedStatusFilter()
    {
        // Refresh the data when filter changes
    }

    public function updatedSearchTerm()
    {
        // Refresh the data when search term changes
    }
}