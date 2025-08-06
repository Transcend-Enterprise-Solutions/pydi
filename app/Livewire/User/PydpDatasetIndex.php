<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\{WithPagination, WithFileUploads};
use App\Models\{PydpDataset, PydpType};

#[Layout('layouts.app')]
#[Title('PYDP Datasets')]
class PydpDatasetIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $showEntries = 10;
    public $search = '';
    public $types = [];

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

    public $valueId, $title, $description, $type;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required'
    ];

    public function mount()
    {
        $this->types = PydpType::all();
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
                'name' => $this->title,
                'description' => $this->description,
            ]);
        } else {
            PydpDataset::create([
                'user_id' => auth()->id(),
                'pydp_type_id' => $this->type,
                'name' => $this->title,
                'description' => $this->description
            ]);
        }

        session()->flash('success', $this->editMode ? 'Dataset updated successfully.' : 'Dataset created successfully.');
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
            PydpDataset::findOrFail($this->valueId)->delete();
            session()->flash('success', 'Indicator deleted successfully!');
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

        session()->flash('success', 'Edit request has been sent successfully!');
        $this->showRequestEditModal = false;
    }

    public function render()
    {
        $tableDatas = PydpDataset::where('user_id', auth()->id())
            ->where('name', 'like', "%{$this->search}%")
            ->latest()->paginate($this->showEntries);

        return view('livewire.user.pydp-dataset-index', compact('tableDatas'));
    }
}
