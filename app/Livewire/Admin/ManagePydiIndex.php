<?php

namespace App\Livewire\Admin;

use App\Mail\AdminActionNotif;
use App\Models\EmailTemplate;
use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\WithPagination;
use App\Models\PydiDataset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.app')]
#[Title('Manage PYDI Datasets')]
class ManagePydiIndex extends Component
{
    use WithPagination;

    public $showEntries = 10;
    public $search = '';

    public $showActionModal = false;
    public $selectedDatasetId;
    public $action_status;
    public $action_feedback;

    public $showMessageModal = false;
    public $feedbackMessage = '';

    public $datasetId;

    public $showEditRequestModal = false;
    public $selectedEditRequestId;
    public $approveReason = '';


    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Action Modal Handling 
    public function openActionModal($id)
    {
        $this->selectedDatasetId = $id;
        $dataset = PydiDataset::findOrFail($id);
        $this->action_status = $dataset->status;
        $this->action_feedback = $dataset->feedback ?? '';
        $this->showActionModal = true;
    }

    public function submitAction()
    {
        $this->validate([
            'action_status' => 'required|in:pending,approved,rejected,needs_revision',
            'action_feedback' => 'nullable|string|max:500',
        ]);

        $dataset = PydiDataset::findOrFail($this->selectedDatasetId);


        $dataset->status = $this->action_status;
        $dataset->feedback = $this->action_feedback ?? null;
        $dataset->is_submitted = $dataset->status !== 'needs_revision';
        $dataset->reviewer_id = auth()->id();
        $dataset->finalized_at = now();
        $dataset->save();

        $userInfo = User::where('users.id', $dataset->user_id)->first();


        $details = null;
        if($dataset){
            $status = str_replace('_', ' ', $this->action_status);
            $details = 'Submission Status: ' . ucfirst($status) . '<br>';
            if($this->action_feedback){
                $details .= 'Feedback: ' . $this->action_feedback;
            }
        }

        $emailTemplate = EmailTemplate::where('name', 'pydi_admin_action_notif')->first();
        if($emailTemplate && $emailTemplate->is_active){
            Mail::to( $userInfo ? $userInfo->email : 'test@gmail.com')->send(new AdminActionNotif( Auth::user()->email, 'pydi_admin_action_notif', $details));
        }else{
            session()->flash('error', 'Email not sent. Email template is not active.');
        }
        
        session()->flash('success', 'Dataset status updated successfully!');
        $this->showActionModal = false;
    }

    // Message Modal Handling
    public function message($id)
    {
        $dataset = PydiDataset::find($id);

        $this->feedbackMessage = $dataset->feedback ?? 'No feedback provided yet.';
        $this->showMessageModal = true;
    }

    // Edit Request Handling
    public function showEditRequest($id)
    {
        $this->selectedEditRequestId = $id;
        $this->showEditRequestModal = true;
    }

    public function processEditRequest($status)
    {
        $action = $status === 'approve' ? 2 : 3;

        $entry = PydiDataset::find($this->selectedEditRequestId);

        if ($entry) {
            $entry->update([
                'is_request_edit' => $action
            ]);
            
            $userInfo = User::where('users.id', $entry->user_id)->first();


            $details = 'Request Status: ' . ucfirst($status) . '<br>';
            if($this->action_feedback){
                $details .= 'Feedback: ' . $this->action_feedback;
            }

            $emailTemplate = EmailTemplate::where('name', 'edit_request_admin_action_notif')->first();
            if($emailTemplate && $emailTemplate->is_active){
                Mail::to( $userInfo ? $userInfo->email : 'test@gmail.com')->send(new AdminActionNotif( Auth::user()->email, 'edit_request_admin_action_notif', $details));
            }else{
                session()->flash('error', 'Email not sent. Email template is not active.');
            }
            
            session()->flash('success', 'Edit request has been processed successfully!');
        } else {
            session()->flash('error', 'Dataset not found.');
        }

        $this->showEditRequestModal = false;
    }

    public function render()
    {
        $query = PydiDataset::query()
            ->whereNotNull('submitted_at')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.admin.manage-pydi-index', compact('tableDatas'));
    }
}
