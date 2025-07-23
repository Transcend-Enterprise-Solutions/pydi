<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\WithPagination;
use App\Models\PydiDataset;

#[Layout('layouts.app')]
#[Title('Manage PYDI Datasets')]
class ManagePydiIndex extends Component
{
    use WithPagination;

    public $showEntries = 10;
    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $showDeleteModal = false;

    public $showActionModal = false;
    public $selectedDatasetId;
    public $action_status;
    public $action_feedback;

    public $showMessageModal = false;
    public $feedbackMessage = '';

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
        $dataset->reviewed_by = auth()->id();
        $dataset->finalized_at = now();
        $dataset->save();

        session()->flash('success', 'Dataset status updated successfully!');
        $this->showActionModal = false;
    }

    public function message($id)
    {
        $dataset = PydiDataset::find($id);

        $this->feedbackMessage = $dataset->feedback ?? 'No feedback provided yet.';
        $this->showMessageModal = true;
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
