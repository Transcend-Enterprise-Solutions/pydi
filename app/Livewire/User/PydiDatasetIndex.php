<?php

namespace App\Livewire\User;

use App\Mail\UserActionNotif;
use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\{WithPagination, WithFileUploads};
use App\Models\PydiDataset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.app')]
#[Title('PYDI Datasets')]
class PydiDatasetIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $showEntries = 10;
    public $search = '';
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

    public $datasetId, $name, $description, $year;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'year' => 'required|integer|min:2000|max:2100'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['datasetId', 'name', 'description', 'year']);
        $this->year = date('Y');
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $dataset = PydiDataset::findOrFail($id);
        $this->datasetId = $dataset->id;
        $this->name = $dataset->name;
        $this->description = $dataset->description;
        $this->year = $dataset->year;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode && $this->datasetId) {
            PydiDataset::findOrFail($this->datasetId)->update([
                'name' => $this->name,
                'description' => $this->description,
                'year' => $this->year,
            ]);
            session()->flash('success', 'Dataset updated successfully!');
        } else {
            PydiDataset::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'description' => $this->description,
                'year' => $this->year,
            ]);
            session()->flash('success', 'Dataset created successfully!');
        }

        $this->reset(['showModal', 'datasetId', 'name', 'description', 'year', 'editMode']);
    }

    public function delete()
    {
        if ($this->datasetId) {
            PydiDataset::findOrFail($this->datasetId)->delete();
            session()->flash('success', 'Dataset deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'datasetId']);
    }

    public function confirmDelete($id)
    {
        $this->datasetId = $id;
        $this->showDeleteModal = true;
    }

    public function message($id)
    {
        $dataset = PydiDataset::find($id);

        $this->feedbackMessage = $dataset->feedback ?? 'No feedback provided yet.';
        $this->showMessageModal = true;
    }

    public function confirmSend($id)
    {
        $this->selectedId = $id;
        $this->showConfirmSend = true;
    }

    public function sendConfirmed()
    {
        if ($this->selectedId) {
            $dataset = PydiDataset::find($this->selectedId);

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
                'PYDI Dataset: ' . $dataset->name . '<br>' .
                'Description: ' . $dataset->description;
            }

            Mail::to('jhonfrancisduarte12345@gmail.com')->send(new UserActionNotif( Auth::user()->email, 'user_dataset_submission','PYDI', $details));

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
        $entry = PydiDataset::find($this->selectedEntryId);
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
            'PYDI Dataset: ' . $entry->name . '<br>' .
            'Description: ' . $entry->description;
        }

        Mail::to('jhonfrancisduarte12345@gmail.com')->send(new UserActionNotif( Auth::user()->email, 'user_request_edit_notif', 'PYDI',  $details));

        session()->flash('success', 'Edit request has been sent successfully!');
        $this->showRequestEditModal = false;
    }

    public function render()
    {
        $query = PydiDataset::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.user.pydi-dataset-index', compact('tableDatas'));
    }
}
