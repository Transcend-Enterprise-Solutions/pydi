<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use Livewire\WithPagination;
use App\Models\{PydpType};

#[Layout('layouts.app')]
#[Title('PYDP Indicator Management')]
class CoverYearIndex extends Component
{
    use WithPagination;
    public $showEntries = 10;
    public $search = '';

    public $showModal = false;
    public $editMode = false;
    public $showDeleteModal = false;

    public $valueId, $title, $description, $yearStart, $yearEnd;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'yearStart' => 'required|integer|min:2000|max:2100',
        'yearEnd' => 'required|integer|min:2000|max:2100'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['valueId', 'title', 'description', 'yearStart', 'yearEnd']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $indicator = PydpType::findOrFail($id);
        $this->valueId = $indicator->id;
        $this->title = $indicator->title;
        $this->description = $indicator->content;
        $this->yearStart = $indicator->year_start;
        $this->yearEnd = $indicator->year_end;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $indicator = PydpType::findOrFail($this->valueId);
            $indicator->update([
                'title' => $this->title,
                'content' => $this->description,
                'year_start' => $this->yearStart,
                'year_end' => $this->yearEnd,
            ]);
        } else {
            PydpType::create([
                'title' => $this->title,
                'content' => $this->description,
                'year_start' => $this->yearStart,
                'year_end' => $this->yearEnd,
            ]);
        }

        session()->flash('success', $this->editMode ? 'Indicator updated successfully.' : 'Indicator created successfully.');
        $this->showModal = false;
        $this->reset(['valueId', 'title', 'description', 'yearStart', 'yearEnd']);
    }

    public function confirmDelete($id)
    {
        $this->valueId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->valueId) {
            PydpType::findOrFail($this->valueId)->delete();
            session()->flash('success', 'Indicator deleted successfully!');
        }

        $this->reset(['showDeleteModal', 'valueId']);
    }

    public function render()
    {
        $tableDatas = PydpType::where('title', 'like', "%{$this->search}%")->latest()->paginate($this->showEntries);

        return view('livewire.admin.cover-year-index', compact('tableDatas'));
    }
}
