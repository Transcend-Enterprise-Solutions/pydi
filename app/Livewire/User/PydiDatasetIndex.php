<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\{WithPagination, WithFileUploads};
use App\Models\PydiDataset;
use App\Models\Indicator;
use App\Services\EmailService;

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

    public $datasetId, $indicator_id, $name, $description, $year;

    protected $rules = [
        'indicator_id' => 'required',
        'name' => 'required_if:indicator_id,other|string|max:255',
        'description' => 'required|string',
        'year' => 'required|integer|min:2000|max:2100'
    ];

    protected $messages = [
        'indicator_id.required' => 'Please select an indicator.',
        'name.required_if' => 'Indicator name is required when "Other Indicator" is selected.',
    ];

    protected EmailService $emailService;

    public function __construct()
    {
        $this->emailService = app(EmailService::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['datasetId', 'indicator_id', 'name', 'description', 'year']);
        $this->year = date('Y');
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $dataset = PydiDataset::findOrFail($id);
        $this->datasetId = $dataset->id;

        // Check if the dataset has an indicator_id
        if ($dataset->indicator_id) {
            $this->indicator_id = $dataset->indicator_id;
        } else {
            // If no indicator_id, it's a custom indicator
            $this->indicator_id = 'other';
            $this->name = $dataset->name;
        }

        $this->description = $dataset->description;
        $this->year = $dataset->year;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function onIndicatorChange()
    {
        // If a predefined indicator is selected, clear the custom name
        if ($this->indicator_id !== 'other') {
            $this->name = '';
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'description' => $this->description,
            'year' => $this->year,
        ];

        // If "Other" is selected, use custom name and no indicator_id
        if ($this->indicator_id === 'other') {
            $data['name'] = $this->name;
            $data['indicator_id'] = null;
        } else {
            // Use the selected indicator
            $indicator = Indicator::with('dimension')->find($this->indicator_id);
            if ($indicator) {
                if ($indicator->dimension) {
                    $data['name'] = $indicator->dimension->name . ' - ' . $indicator->name;
                } else {
                    $data['name'] = $indicator->name;
                }
                $data['indicator_id'] = $this->indicator_id;
            }
        }

        if ($this->editMode && $this->datasetId) {
            PydiDataset::findOrFail($this->datasetId)->update($data);
            session()->flash('success', 'Dataset updated successfully!');
        } else {
            $data['user_id'] = auth()->id();
            PydiDataset::create($data);
            session()->flash('success', 'Dataset created successfully!');
        }

        $this->reset(['showModal', 'datasetId', 'indicator_id', 'name', 'description', 'year', 'editMode']);
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

            // Send email notif ---------------------------------------------------------- //
            $result = $this->emailService->sendEmailNotificationForAdmin($dataset, 'pydi_dataset_submission', 'PYDI');

            if($result['success']){
                session()->flash('success', $result['message']);
            }else{
                session()->flash('error', $result['message']);
            }

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

        // Send email notification
        $result = $this->emailService->sendEmailNotificationForAdmin($entry, 'pydi_request_edit_notif', 'PYDI');

        if($result['success']){
            session()->flash('success', $result['message']);
        }else{
            session()->flash('error', $result['message']);
        }

        session()->flash('success', 'Edit request has been sent successfully!');
        $this->showRequestEditModal = false;
    }

    public function render()
    {
        // Get all indicators - check if dimension relationship exists
        try {
            $indicators = Indicator::with('dimension')->get();
        } catch (\Exception $e) {
            // Fallback if dimension relationship doesn't exist
            $indicators = Indicator::all();
        }

        $query = PydiDataset::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            });

        $tableDatas = $query->latest()->paginate($this->showEntries);

        return view('livewire.user.pydi-dataset-index', compact('tableDatas', 'indicators'));
    }
}
