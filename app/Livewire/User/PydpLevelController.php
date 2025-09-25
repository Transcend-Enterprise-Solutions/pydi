<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\{PydpLevel, PydpIndicator, UserLog};
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Levels Indicators | PYDI')]
class PydpLevelController extends Component
{
    public $dimensions;
    public $showDimensionModal = false;
    public $showIndicatorModal = false;

    // Form fields
    public $dimensionName = '';
    public $dimensionDescription = '';
    public $editingDimensionId = null;

    public $indicatorName = '';
    public $indicatorDescription = '';
    public $selectedDimensionId = null;
    public $editingIndicatorId = null;
    public $showDeleteModal = false;

    public $valueId, $type;

    public function mount()
    {
        $this->loadDimensions();
    }

    public function loadDimensions()
    {
        // Only load dimensions belonging to the authenticated user
        $this->dimensions = PydpLevel::where('user_id', auth()->id())
            ->with('indicators')
            ->orderBy('title')
            ->get();
    }

    public function openDimensionModal($dimensionId = null)
    {
        if ($dimensionId) {
            // Ensure the dimension belongs to the authenticated user
            $dimension = PydpLevel::where('user_id', auth()->id())
                ->where('id', $dimensionId)
                ->first();

            if (!$dimension) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Level not found or access denied.',
                    'icon' => 'error'
                ]);
                return;
            }

            $this->editingDimensionId = $dimensionId;
            $this->dimensionName = $dimension->title;
            $this->dimensionDescription = $dimension->content;
        } else {
            $this->resetDimensionForm();
        }
        $this->showDimensionModal = true;
    }

    public function openIndicatorModal($dimensionId, $indicatorId = null)
    {
        // Verify the dimension belongs to the authenticated user
        $dimension = PydpLevel::where('user_id', auth()->id())
            ->where('id', $dimensionId)
            ->first();

        if (!$dimension) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Level not found or access denied.',
                'icon' => 'error'
            ]);
            return;
        }

        $this->selectedDimensionId = $dimensionId;

        if ($indicatorId) {
            // Ensure the indicator belongs to a level owned by the authenticated user
            $indicator = PydpIndicator::whereHas('level', function ($query) {
                $query->where('user_id', auth()->id());
            })->where('id', $indicatorId)->first();

            if (!$indicator) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Indicator not found or access denied.',
                    'icon' => 'error'
                ]);
                return;
            }

            $this->editingIndicatorId = $indicatorId;
            $this->indicatorName = $indicator->title;
            $this->indicatorDescription = $indicator->content;
        } else {
            $this->resetIndicatorForm();
            $this->selectedDimensionId = $dimensionId;
        }
        $this->showIndicatorModal = true;
    }

    public function closeDimensionModal()
    {
        $this->showDimensionModal = false;
        $this->resetDimensionForm();
    }

    public function closeIndicatorModal()
    {
        $this->showIndicatorModal = false;
        $this->resetIndicatorFormCompletely();
    }

    public function confirmAction($id, $type)
    {
        $this->showDeleteModal = true;
        $this->valueId = $id;
        $this->type = $type === 'deleteDimension' ? 'level' : 'indicator';
    }

    public function confirmDelete()
    {
        if ($this->type == 'level') {
            // Ensure the level belongs to the authenticated user
            $level = PydpLevel::where('user_id', auth()->id())
                ->where('id', $this->valueId)
                ->first();

            if (!$level) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Level not found or access denied.',
                    'icon' => 'error'
                ]);
                $this->showDeleteModal = false;
                return;
            }

            $name = $level->title;
            $level->delete();

            $this->logs("Deleted level: {$name}");

            $this->loadDimensions();
            $this->dispatch('swal', [
                'title' => 'Deleted!',
                'text' => 'Level deleted successfully.',
                'icon' => 'success'
            ]);
        } elseif ($this->type == 'indicator') {
            // Ensure the indicator belongs to a level owned by the authenticated user
            $indicator = PydpIndicator::whereHas('level', function ($query) {
                $query->where('user_id', auth()->id());
            })->where('id', $this->valueId)->first();

            if (!$indicator) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Indicator not found or access denied.',
                    'icon' => 'error'
                ]);
                $this->showDeleteModal = false;
                return;
            }

            $name = $indicator->title;
            $indicator->delete();

            $this->logs("Deleted indicator: {$name}");

            $this->loadDimensions();
            $this->dispatch('swal', [
                'title' => 'Deleted!',
                'text' => 'Indicator deleted successfully.',
                'icon' => 'success'
            ]);
        }

        $this->showDeleteModal = false;
    }

    public function saveDimension()
    {
        $this->validate([
            'dimensionName' => 'required|min:3',
            'dimensionDescription' => 'nullable'
        ]);

        if ($this->editingDimensionId) {
            // Ensure the dimension belongs to the authenticated user
            $dimension = PydpLevel::where('user_id', auth()->id())
                ->where('id', $this->editingDimensionId)
                ->first();

            if (!$dimension) {
                $this->dispatch('swal', [
                    'title' => 'Error!',
                    'text' => 'Level not found or access denied.',
                    'icon' => 'error'
                ]);
                return;
            }

            $dimension->update([
                'title' => $this->dimensionName,
                'content' => $this->dimensionDescription
            ]);
            $message = 'Level updated successfully';
            $action = "Updated level: {$this->dimensionName}";
        } else {
            PydpLevel::create([
                'user_id' => auth()->id(),
                'title' => $this->dimensionName,
                'content' => $this->dimensionDescription
            ]);
            $message = 'Level created successfully';
            $action = "Created level: {$this->dimensionName}";
        }

        // save log
        $this->logs($action);

        $this->closeDimensionModal();
        $this->loadDimensions();
        $this->dispatch('swal', [
            'title' => 'Success!',
            'text' => $message,
            'icon' => 'success'
        ]);
    }

    public function saveIndicator()
    {
        $this->validate([
            'indicatorName' => 'required|min:3',
            'indicatorDescription' => 'nullable',
            'selectedDimensionId' => 'required|exists:pydp_levels,id'
        ]);

        // Verify the selected dimension belongs to the authenticated user
        $dimension = PydpLevel::where('user_id', auth()->id())
            ->where('id', $this->selectedDimensionId)
            ->first();

        if (!$dimension) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Selected level not found or access denied.',
                'icon' => 'error'
            ]);
            return;
        }

        try {
            if ($this->editingIndicatorId) {
                // Ensure the indicator belongs to a level owned by the authenticated user
                $indicator = PydpIndicator::whereHas('level', function ($query) {
                    $query->where('user_id', auth()->id());
                })->where('id', $this->editingIndicatorId)->first();

                if (!$indicator) {
                    $this->dispatch('swal', [
                        'title' => 'Error!',
                        'text' => 'Indicator not found or access denied.',
                        'icon' => 'error'
                    ]);
                    return;
                }

                $indicator->update([
                    'title' => $this->indicatorName,
                    'content' => $this->indicatorDescription,
                ]);
                $message = 'Indicator updated successfully';
                $action = "Updated indicator: {$this->indicatorName}";
            } else {
                PydpIndicator::create([
                    'pydp_level_id' => $this->selectedDimensionId,
                    'title' => $this->indicatorName,
                    'content' => $this->indicatorDescription,
                ]);
                $message = 'Indicator created successfully';
                $action = "Created indicator: {$this->indicatorName}";
            }

            // save log
            $this->logs($action);

            $this->closeIndicatorModal();
            $this->loadDimensions();
            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => $message,
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Failed to save indicator: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function logs($action)
    {
        UserLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
        ]);
    }

    public function resetDimensionForm()
    {
        $this->dimensionName = '';
        $this->dimensionDescription = '';
        $this->editingDimensionId = null;
    }

    public function resetIndicatorForm()
    {
        $this->indicatorName = '';
        $this->indicatorDescription = '';
        $this->editingIndicatorId = null;
    }

    public function resetIndicatorFormCompletely()
    {
        $this->indicatorName = '';
        $this->indicatorDescription = '';
        $this->editingIndicatorId = null;
        $this->selectedDimensionId = null;
    }

    public function render()
    {
        return view('livewire.user.pydp-level-controller', [
            'selectedDimensionName' => $this->selectedDimensionId
                ? $this->dimensions->find($this->selectedDimensionId)?->title
                : null
        ]);
    }
}
