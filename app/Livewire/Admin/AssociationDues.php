<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Association Dues')]

class AssociationDues extends Component
{
    public function render()
    {
        return view('livewire.admin.association-dues');
    }
}
