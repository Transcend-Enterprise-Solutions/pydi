<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\UserData;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Agency Representatives | PYDI')]
class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $selectedTab = 'all';
    public $perPage = 10;
    public $confirmingAction = false;
    public $actionType = '';
    public $userId = null;

    public function render()
    {
        $query = User::with('userData')
            ->where('user_role', 'user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                      ->orWhereHas('userData', function ($q) {
                          $q->where('first_name', 'like', '%'.$this->search.'%')
                            ->orWhere('last_name', 'like', '%'.$this->search.'%')
                            ->orWhere('government_agency', 'like', '%'.$this->search.'%');
                      });
                });
            })
            ->when($this->selectedTab === 'active', function ($query) {
                return $query->where('active_status', 1);
            })
            ->when($this->selectedTab === 'inactive', function ($query) {
                return $query->where('active_status', '!=', 1);
            })
            ->when($this->statusFilter !== '', function ($query) {
                return $query->where('active_status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        return view('livewire.admin.user-list', [
            'users' => $query->paginate($this->perPage)
        ]);
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
        $this->resetPage();
    }

    public function confirmAction($userId, $actionType)
    {
        $this->userId = $userId;
        $this->actionType = $actionType;
        $this->confirmingAction = true;
    }

    public function updateStatus()
    {
        if ($this->actionType === 'reject') {
            // Delete the user and their data when rejected
            $user = User::findOrFail($this->userId);
            $user->userData()->delete();
            $user->delete();

            $this->dispatch('swal', [
                'title' => 'Rejected!',
                'text' => 'User has been rejected and removed from the system.',
                'icon' => 'success'
            ]);
        } else {
            // Handle other status changes
            $status = match($this->actionType) {
                'approve' => 1,
                'deactivate' => 3,
                default => null
            };

            if ($status !== null) {
                $user = User::findOrFail($this->userId);
                $user->update(['active_status' => $status]);

                $this->dispatch('swal', [
                    'title' => $this->getSuccessTitle(),
                    'text' => $this->getSuccessMessage($status),
                    'icon' => 'success'
                ]);
            }
        }

        $this->resetAction();
    }

    public function resetAction()
    {
        $this->confirmingAction = false;
        $this->userId = null;
        $this->actionType = '';
    }

    private function getSuccessMessage($status)
    {
        return match($status) {
            1 => 'User has been approved successfully!',
            3 => 'User has been deactivated successfully!',
            default => 'Status updated successfully!'
        };
    }

    private function getSuccessTitle()
    {
        return match($this->actionType) {
            'approve' => 'Approved!',
            'deactivate' => 'Deactivated!',
            default => 'Success!'
        };
    }
}
