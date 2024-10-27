<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Electric Bills')]

class ElectricBills extends Component
{
    public function render()
    {
        return view('livewire.admin.electric-bills');
    }
}
