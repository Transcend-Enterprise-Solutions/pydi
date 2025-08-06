<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use Livewire\Attributes\{Title, Layout};
use App\Models\{Dimension};

#[Layout('layouts.land')]
#[Title('PYDI Home')]

class HomeIndex extends Component
{

    public $dimensions = [];

    public function mount()
    {
        $this->dimensions = Dimension::with(['indicators', 'pydiDatasetDetails'])->get();
    }

    public function render()
    {
        return view('livewire.landing.home-index');
    }
}
