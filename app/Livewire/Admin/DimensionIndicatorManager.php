<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Dimension;
use App\Models\Indicator;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Dimension Indicators | PYDI')]
class DimensionIndicatorManager extends Component
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
    public $indicatorUnit = '';
    public $selectedDimensionId = null;
    public $editingIndicatorId = null;

    public function mount()
    {
        $this->loadDimensions();
    }

    public function loadDimensions()
    {
        $this->dimensions = Dimension::with('indicators')->orderBy('name')->get();
    }

    public function openDimensionModal($dimensionId = null)
    {
        if ($dimensionId) {
            $dimension = Dimension::find($dimensionId);
            $this->editingDimensionId = $dimensionId;
            $this->dimensionName = $dimension->name;
            $this->dimensionDescription = $dimension->description;
        } else {
            $this->resetDimensionForm();
        }
        $this->showDimensionModal = true;
    }

    public function openIndicatorModal($dimensionId, $indicatorId = null)
    {
        $this->selectedDimensionId = $dimensionId;

        if ($indicatorId) {
            $indicator = Indicator::find($indicatorId);
            if ($indicator) {
                $this->editingIndicatorId = $indicatorId;
                $this->indicatorName = $indicator->name;
                $this->indicatorDescription = $indicator->description;
                $this->indicatorUnit = $indicator->measurement_unit;
            }
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
        if ($type === 'deleteDimension') {
            Dimension::findOrFail($id)->delete();
            $this->loadDimensions();
            $this->dispatch('swal', [
                'title' => 'Deleted!',
                'text' => 'Dimension deleted successfully.',
                'icon' => 'success'
            ]);
        } elseif ($type === 'deleteIndicator') {
            Indicator::findOrFail($id)->delete();
            $this->loadDimensions();
            $this->dispatch('swal', [
                'title' => 'Deleted!',
                'text' => 'Indicator deleted successfully.',
                'icon' => 'success'
            ]);
        }
    }

    // Dimension CRUD
    public function saveDimension()
    {
        $this->validate([
            'dimensionName' => 'required|min:3',
            'dimensionDescription' => 'nullable'
        ]);

        if ($this->editingDimensionId) {
            $dimension = Dimension::find($this->editingDimensionId);
            $dimension->update([
                'name' => $this->dimensionName,
                'description' => $this->dimensionDescription
            ]);
            $message = 'Dimension updated successfully';
        } else {
            Dimension::create([
                'name' => $this->dimensionName,
                'description' => $this->dimensionDescription
            ]);
            $message = 'Dimension created successfully';
        }

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
            'indicatorUnit' => 'nullable',
            'selectedDimensionId' => 'required|exists:dimensions,id'
        ]);

        try {
            if ($this->editingIndicatorId) {
                $indicator = Indicator::find($this->editingIndicatorId);
                $indicator->update([
                    'name' => $this->indicatorName,
                    'description' => $this->indicatorDescription,
                    'measurement_unit' => $this->indicatorUnit
                ]);
                $message = 'Indicator updated successfully';
            } else {
                Indicator::create([
                    'dimension_id' => $this->selectedDimensionId,
                    'name' => $this->indicatorName,
                    'description' => $this->indicatorDescription,
                    'measurement_unit' => $this->indicatorUnit
                ]);
                $message = 'Indicator created successfully';
            }

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
        $this->indicatorUnit = '';
        $this->editingIndicatorId = null;

    }

    public function resetIndicatorFormCompletely()
    {
        $this->indicatorName = '';
        $this->indicatorDescription = '';
        $this->indicatorUnit = '';
        $this->editingIndicatorId = null;
        $this->selectedDimensionId = null;
    }

    public function render()
    {
        return view('livewire.admin.dimension-indicator-manager', [
            'selectedDimensionName' => $this->selectedDimensionId
                ? $this->dimensions->find($this->selectedDimensionId)?->name
                : null
        ]);
    }
}
