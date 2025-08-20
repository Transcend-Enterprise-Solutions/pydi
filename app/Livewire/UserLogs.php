<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\{UserLog};

#[Layout('layouts.app')]
#[Title('Logs')]
class UserLogs extends Component
{
    use WithPagination;
    public $showEntries = 10;
    public $search = '';

    public function render()
    {
        if (auth()->user()->role === 'user') {
            // Regular user → show only their logs
            $tableDatas = UserLog::with('user')->where('user_id', auth()->id())
                ->where('action', 'like', "%{$this->search}%")
                ->latest()
                ->paginate($this->showEntries);
        } else {
            // Admin (or other roles) → show all logs
            $tableDatas = UserLog::with('user')->where('action', 'like', "%{$this->search}%")
                ->latest()
                ->paginate($this->showEntries);
        }

        return view('livewire.user-logs', compact('tableDatas'));
    }
}
