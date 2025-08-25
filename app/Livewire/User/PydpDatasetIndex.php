<?php

namespace App\Livewire\User;

use App\Mail\UserActionNotif;
use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\{WithPagination, WithFileUploads};
use App\Models\{EmailTemplate, PydpDataset, PydpType, UserLog, PydpLevel, User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.app')]
#[Title('PYDP Datasets')]
class PydpDatasetIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $showEntries = 10;
    public $search = '';
    public $types = [], $levels = [];

    public $showModal = false;
    public $editMode = false;
    public $showDeleteModal = false;

    public $showMessageModal = false;
    public $feedbackMessage = '';

    public $showRequestEditModal = false;
    public $selectedEntryId;

    // Submit Dataset
    public $showConfirmSend = false;
    public $selectedId = null;
    public $file;

    public $valueId, $title, $description, $type, $level;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required'
    ];

    public function mount()
    {
        $this->types = PydpType::all();
        $this->levels = PydpLevel::where('user_id', auth()->id())->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Create and Edit Dataset
    public function create()
    {
        $this->reset(['valueId', 'title', 'description', 'type']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $data = PydpDataset::findOrFail($id);
        $this->valueId = $data->id;
        $this->title = $data->name;
        $this->description = $data->description;
        $this->type = $data->pydp_type_id;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $dataset = PydpDataset::findOrFail($this->valueId);
            $dataset->update([
                'pydp_type_id' => $this->type,
                'pydp_level_id' => $this->level,
                'name' => $this->title,
                'description' => $this->description
            ]);

            $this->logs("Updated dataset: {$this->title}");
            $message = 'Dataset updated successfully.';
        } else {
            $dataset = PydpDataset::create([
                'user_id' => auth()->id(),
                'pydp_type_id' => $this->type,
                'pydp_level_id' => $this->level,
                'name' => $this->title,
                'description' => $this->description
            ]);

            $this->logs("Created dataset: {$this->title}");
            $message = 'Dataset created successfully.';
        }

        session()->flash('success', $message);
        $this->showModal = false;
        $this->reset(['valueId', 'title', 'description', 'type']);
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
            $dataset = PydpDataset::findOrFail($this->valueId);
            $name = $dataset->name;
            $dataset->delete();

            $this->logs("Deleted dataset: {$name}");

            session()->flash('success', 'Dataset deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'valueId']);
    }

    // View Feedback Message
    public function message($id)
    {
        $dataset = PydpDataset::find($id);

        $this->feedbackMessage = $dataset->feedback ?? 'No feedback provided yet.';
        $this->showMessageModal = true;
    }

    // Confirm Send Dataset
    public function confirmSend($id)
    {
        $this->selectedId = $id;
        $this->showConfirmSend = true;
    }

    public function sendConfirmed()
    {
        if ($this->selectedId) {
            $dataset = PydpDataset::find($this->selectedId);

            $this->validate([
                'file' => 'nullable|file|max:2048',
            ]);

            if ($this->file) {
                $filePath = $this->file->store('attachments', 'public');
                $dataset->file_path = $filePath;
            }

            $dataset->is_submitted = true;
            $dataset->status = 'pending';
            $dataset->submitted_at = now();
            $dataset->save();

            $userInfo = User::where('users.id', $dataset->user_id)
                            ->join('user_data', 'user_data.user_id', 'users.id')
                            ->first();

            $details = null;
            if($userInfo){
                $details = 'Agency: ' . $userInfo->government_agency . '<br>' .
                'Representative: ' . $userInfo->name . '<br>' .
                'PYDP Dataset: ' . $dataset->name . '<br>' .
                'Description: ' . $dataset->description;
            }

            $emailTemplate = EmailTemplate::where('name', 'user_dataset_submission')->first();
            if($emailTemplate && $emailTemplate->is_active){
                Mail::to('jhonfrancisduarte12345@gmail.com')->send(new UserActionNotif( Auth::user()->email, 'user_dataset_submission', 'PYDP',  $details));
            }

            
            $this->logs("Submitted dataset: {$dataset->name}");
            session()->flash('success', 'Dataset has been sent successfully!');
            
        }

        $this->reset(['showConfirmSend', 'selectedId', 'file']);
    }

    // request edit
    public function requestEdit($id)
    {
        $this->selectedEntryId = $id;
        $this->showRequestEditModal = true;
    }

    public function confirmRequestEdit()
    {
        $entry = PydpDataset::find($this->selectedEntryId);
        $entry->update([
            'is_request_edit' => true,
        ]);

        $userInfo = User::where('users.id', $entry->user_id)
                        ->join('user_data', 'user_data.user_id', 'users.id')
                        ->first();

        $details = null;
        if($userInfo){
            $details = 'Agency: ' . $userInfo->government_agency . '<br>' .
            'Representative: ' . $userInfo->name . '<br>' .
            'PYDP Dataset: ' . $entry->name . '<br>' .
            'Description: ' . $entry->description;
        }

        $emailTemplate = EmailTemplate::where('name', 'user_request_edit_notif')->first();
        if($emailTemplate && $emailTemplate->is_active){
            Mail::to('jhonfrancisduarte12345@gmail.com')->send(new UserActionNotif( Auth::user()->email, 'user_request_edit_notif', 'PYDP',  $details));
        }

        $this->logs("Requested edit for dataset: {$entry->name}");

        session()->flash('success', 'Edit request has been sent successfully!');
        $this->showRequestEditModal = false;
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
        $tableDatas = PydpDataset::where('user_id', auth()->id())
            ->where('name', 'like', "%{$this->search}%")
            ->latest()->paginate($this->showEntries);

        return view('livewire.user.pydp-dataset-index', compact('tableDatas'));
    }
}
