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
#[Title('PYDP Accomplishments')]
class PydpDatasetIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Properties for pagination and search
    public $showEntries = 10;
    public $search = '';
    public $types = [];

    // Modal states
    public $showModal = false;
    public $editMode = false;
    public $showDeleteModal = false;
    public $showMessageModal = false;
    public $showRequestEditModal = false;
    public $showConfirmSend = false;

    // Modal data
    public $feedbackMessage = '';
    public $selectedEntryId;
    public $selectedId = null;
    public $file;

    // Form fields
    public $valueId, $title, $description, $type;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required'
    ];

    protected $messages = [
        'title.required' => 'The title field is required.',
        'description.required' => 'The description field is required.',
        'type.required' => 'Please select a year coverage.',
    ];

    public function mount()
    {
        $this->types = PydpType::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ======================
    // CREATE AND EDIT METHODS
    // ======================

    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function openCreateModal()
    {
        $this->create();
    }

    public function edit($id)
    {
        try {
            $data = PydpDataset::findOrFail($id);
            $this->valueId = $data->id;
            $this->title = $data->name;
            $this->description = $data->description;
            $this->type = $data->pydp_type_id;

            $this->editMode = true;
            $this->showModal = true;
            $this->dispatch('modalOpened');
        } catch (\Exception $e) {
            session()->flash('error', 'Dataset not found.');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $dataset = PydpDataset::findOrFail($this->valueId);
                $dataset->update([
                    'pydp_type_id' => $this->type,
                    'name' => $this->title,
                    'description' => $this->description
                ]);

                $this->logs("Updated dataset: {$this->title}");
                $message = 'Dataset updated successfully.';
            } else {
                $dataset = PydpDataset::create([
                    'user_id' => auth()->id(),
                    'pydp_type_id' => $this->type,
                    'name' => $this->title,
                    'description' => $this->description
                ]);

                $this->logs("Created dataset: {$this->title}");
                $message = 'Dataset created successfully.';
            }

            session()->flash('success', $message);
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the dataset.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset(['valueId', 'title', 'description', 'type']);
    }

    // ======================
    // DELETE METHODS
    // ======================

    public function confirmDelete($id)
    {
        $this->valueId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            if ($this->valueId) {
                $dataset = PydpDataset::findOrFail($this->valueId);
                $name = $dataset->name;
                $dataset->delete();

                $this->logs("Deleted dataset: {$name}");
                session()->flash('success', 'Dataset deleted successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the dataset.');
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['valueId']);
    }

    // ======================
    // MESSAGE METHODS
    // ======================

    public function message($id)
    {
        try {
            $dataset = PydpDataset::findOrFail($id);
            $this->feedbackMessage = $dataset->feedback ?? 'No feedback provided yet.';
            $this->showMessageModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Dataset not found.');
        }
    }

    public function closeMessageModal()
    {
        $this->showMessageModal = false;
        $this->feedbackMessage = '';
    }

    // ======================
    // SEND DATASET METHODS
    // ======================

    public function confirmSend($id)
    {
        $this->selectedId = $id;
        $this->showConfirmSend = true;
    }

    public function sendConfirmed()
    {
        try {
            if ($this->selectedId) {
                $dataset = PydpDataset::findOrFail($this->selectedId);

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

                // Send email notification
                $this->sendEmailNotification($dataset, 'user_dataset_submission');

                $this->logs("Submitted dataset: {$dataset->name}");
                session()->flash('success', 'Dataset has been sent successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while submitting the dataset.');
        }

        $this->closeSendModal();
    }

    public function closeSendModal()
    {
        $this->showConfirmSend = false;
        $this->reset(['selectedId', 'file']);
    }

    // ======================
    // REQUEST EDIT METHODS
    // ======================

    public function requestEdit($id)
    {
        $this->selectedEntryId = $id;
        $this->showRequestEditModal = true;
    }

    public function confirmRequestEdit()
    {
        try {
            $entry = PydpDataset::findOrFail($this->selectedEntryId);
            $entry->update([
                'is_request_edit' => true,
            ]);

            // Send email notification
            $this->sendEmailNotification($entry, 'user_request_edit_notif');

            $this->logs("Requested edit for dataset: {$entry->name}");
            session()->flash('success', 'Edit request has been sent successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while sending the edit request.');
        }

        $this->closeRequestEditModal();
    }

    public function closeRequestEditModal()
    {
        $this->showRequestEditModal = false;
        $this->selectedEntryId = null;
    }

    // ======================
    // HELPER METHODS
    // ======================

    private function sendEmailNotification($dataset, $templateName)
    {
        try {
            $userInfo = User::where('users.id', $dataset->user_id)
                            ->join('user_data', 'user_data.user_id', 'users.id')
                            ->first();

            if ($userInfo) {
                $details = 'Agency: ' . $userInfo->government_agency . '<br>' .
                          'Representative: ' . $userInfo->name . '<br>' .
                          'PYDP Dataset: ' . $dataset->name . '<br>' .
                          'Description: ' . $dataset->description;

                $emailTemplate = EmailTemplate::where('name', $templateName)->first();
                if ($emailTemplate && $emailTemplate->is_active) {
                    Mail::to('jhonfrancisduarte12345@gmail.com')->send(
                        new UserActionNotif(Auth::user()->email, $templateName, 'PYDP', $details)
                    );
                }
            }
        } catch (\Exception $e) {
            // Log email error but don't throw exception

        }
    }

    public function logs($action)
    {
        try {
            UserLog::create([
                'user_id' => auth()->id(),
                'action'  => $action . ' at ' . now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Log error but don't throw exception

        }
    }


    public function render()
    {
        $tableDatas = PydpDataset::where('user_id', auth()->id())
            ->where('name', 'like', "%{$this->search}%")
            ->with(['type']) // Eager load the relationship
            ->latest()
            ->paginate($this->showEntries);

        return view('livewire.user.pydp-dataset-index', compact('tableDatas'));
    }
}
