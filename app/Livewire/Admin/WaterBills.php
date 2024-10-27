<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Water Bills')]

class WaterBills extends Component
{
    public function render()
    {
        return view('livewire.admin.water-bills');
    }
}
