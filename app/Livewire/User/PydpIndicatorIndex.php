<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\WithPagination;
use App\Models\{PydpIndicator};

#[Layout('layouts.app')]
#[Title('PYDP Indicator')]
class PydpIndicatorIndex extends Component
{
    use WithPagination;
    public $showEntries = 10;
    public $search = '';

    public $showModal = false;
    public $editMode = false;
    public $showDeleteModal = false;

    public $valueId, $title, $description;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['valueId', 'title', 'description']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $indicator = PydpIndicator::findOrFail($id);
        $this->valueId = $indicator->id;
        $this->title = $indicator->title;
        $this->description = $indicator->content;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $indicator = PydpIndicator::findOrFail($this->valueId);
            $indicator->update([
                'title' => $this->title,
                'content' => $this->description,
            ]);
        } else {
            PydpIndicator::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'content' => $this->description
            ]);
        }

        session()->flash('success', $this->editMode ? 'Indicator updated successfully.' : 'Indicator created successfully.');
        $this->showModal = false;
        $this->reset(['valueId', 'title', 'description']);
    }

    public function confirmDelete($id)
    {
        $this->valueId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->valueId) {
            PydpIndicator::findOrFail($this->valueId)->delete();
            session()->flash('success', 'Indicator deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'valueId']);
    }

    public function render()
    {
        $tableDatas = PydpIndicator::with('type')->where('user_id', auth()->id())
            ->where('title', 'like', "%{$this->search}%")->latest()->paginate($this->showEntries);

        return view('livewire.user.pydp-indicator-index', compact('tableDatas'));
    }
}
