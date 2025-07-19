<?php
namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Indicator;
use App\Models\Dimension;
use App\Models\Accomplishments;

#[Layout('layouts.app')]
#[Title('Input Datasets | PYDI')]
class InputDatasets extends Component
{
    public $showModal = false;
    public $showDeleteModal = false;
    public $showFeedbackModal = false;
    public $selectedPPA = '';
    public $selectedIndicator = '';
    public $ppaToDelete = '';
    public $indicatorIdToDelete = '';
    public $feedbackData = null;
    public $year = '';
    public $targets = [];
    public $actuals = [];
    public $indicators = [];
    
    // Available years (2024 to 2028 based on your table)
    public $availableYears = [
        '2024', '2025', '2026', '2027', '2028'
    ];

    public function mount()
    {
        $this->year = '2024';
        $this->loadIndicators();
        $this->initializeData();
    }

    public function showDeleteConfirmation($ppaName, $indicatorId)
    {
        $this->ppaToDelete = $ppaName;
        $this->indicatorIdToDelete = $indicatorId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['ppaToDelete', 'indicatorIdToDelete']);
    }

    public function confirmDelete()
    {
        Accomplishments::where('ppa_name', $this->ppaToDelete)
            ->where('indicator_id', $this->indicatorIdToDelete)
            ->delete();
            
        $this->dispatch('swal', [
            'title' => 'Success',
            'text' => 'Accomplishment deleted successfully!',
            'icon' => 'success',
        ]);
        $this->closeDeleteModal();
    }

    public function showFeedback($ppaName, $indicatorId)
    {
        $this->feedbackData = Accomplishments::where('ppa_name', $ppaName)
            ->where('indicator_id', $indicatorId)
            ->with(['reviewer', 'indicator'])
            ->get();
        $this->showFeedbackModal = true;
    }

    public function closeFeedbackModal()
    {
        $this->showFeedbackModal = false;
        $this->feedbackData = null;
    }

    public function loadIndicators()
    {
        $this->indicators = Indicator::with('dimension')->get();
    }

    public function initializeData()
    {
        foreach ($this->availableYears as $year) {
            $this->targets[$year] = [
                'physical' => '',
                'financial' => ''
            ];
            $this->actuals[$year] = [
                'physical' => '',
                'financial' => ''
            ];
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['selectedPPA', 'selectedIndicator', 'targets', 'actuals']);
        $this->initializeData();
    }

    public function saveAccomplishment()
    {
        $this->validate([
            'selectedPPA' => 'required|string',
            'selectedIndicator' => 'required|exists:indicators,id',
            'targets.*.physical' => 'nullable|numeric',
            'targets.*.financial' => 'nullable|numeric',
            'actuals.*.physical' => 'nullable|numeric', 
            'actuals.*.financial' => 'nullable|numeric',
        ]);

        foreach ($this->availableYears as $year) {
            Accomplishments::updateOrCreate(
                [
                    'indicator_id' => $this->selectedIndicator,
                    'ppa_name' => $this->selectedPPA,
                    'year' => $year
                ],
                [
                    'target_physical' => $this->targets[$year]['physical'] ?: null,
                    'target_financial' => $this->targets[$year]['financial'] ?: null,
                    'actual_physical' => $this->actuals[$year]['physical'] ?: null,
                    'actual_financial' => $this->actuals[$year]['financial'] ?: null,
                    'status' => 'pending', // Reset status to pending when data is updated
                    'admin_feedback' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null
                ]
            );
        }
        
        $this->dispatch('swal', [
            'title' => 'Success',
            'text' => 'Accomplishment data submitted for review!',
            'icon' => 'success',
        ]);
        
        $this->closeModal();
    }

    public function render()
    {
        $accomplishments = $this->getAccomplishmentsData();
        
        return view('livewire.user.input-datasets', [
            'accomplishments' => $accomplishments
        ]);
    }

    public function getAccomplishmentsData()
    {
        $accomplishments = Accomplishments::with(['indicator.dimension', 'reviewer'])
            ->orderBy('ppa_name')
            ->orderBy('indicator_id')
            ->orderBy('year')
            ->get();

        $groupedData = [];
        
        foreach ($accomplishments as $accomplishment) {
            $key = $accomplishment->ppa_name . '_' . $accomplishment->indicator_id;
            
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'ppa_name' => $accomplishment->ppa_name,
                    'indicator' => $accomplishment->indicator,
                    'years' => [],
                    'overall_status' => 'pending',
                    'has_feedback' => false
                ];
            }
            
            $groupedData[$key]['years'][$accomplishment->year] = $accomplishment;
            
            // Determine overall status (if any year is rejected/needs_revision, show that)
            if ($accomplishment->status === 'rejected' || $accomplishment->status === 'needs_revision') {
                $groupedData[$key]['overall_status'] = $accomplishment->status;
                $groupedData[$key]['has_feedback'] = !empty($accomplishment->admin_feedback);
            } elseif ($accomplishment->status === 'approved' && $groupedData[$key]['overall_status'] === 'pending') {
                $groupedData[$key]['overall_status'] = 'approved';
            }
        }
        
        return $groupedData;
    }

    public function deleteAccomplishment($ppaName, $indicatorId)
    {
        Accomplishments::where('ppa_name', $ppaName)
            ->where('indicator_id', $indicatorId)
            ->delete();
            
        $this->dispatch('swal', [
            'title' => 'Success',
            'text' => 'Accomplishment deleted successfully!',
            'icon' => 'success',
        ]);
    }

    public function editAccomplishment($ppaName, $indicatorId)
    {
        $this->selectedPPA = $ppaName;
        $this->selectedIndicator = $indicatorId;
        
        $existingData = Accomplishments::where('ppa_name', $ppaName)
            ->where('indicator_id', $indicatorId)
            ->get();
            
        foreach ($existingData as $data) {
            $this->targets[$data->year] = [
                'physical' => $data->target_physical,
                'financial' => $data->target_financial
            ];
            $this->actuals[$data->year] = [
                'physical' => $data->actual_physical,
                'financial' => $data->actual_financial
            ];
        }
        
        $this->showModal = true;
    }

    public function resetVariables()
    {
        $this->reset(['selectedPPA', 'selectedIndicator', 'targets', 'actuals']);
        $this->initializeData();
    }
}