<?php

namespace App\Livewire\Admin;

use App\Jobs\SendBulkEmailJob;
use App\Mail\SubmissionReminderNotif;
use App\Models\EmailTemplate;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\UserData;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
    public $bulkAction = '';
    public $openEmailModal = false;
    public $openBulkEmailModal = false;
    public $email_subject;
    public $actionType = '';
    public $userId = null;
    
    // Bulk selection properties
    public $bulkSelectMode = false;
    public $selectedUsers = [];
    public $selectAll = false;
    public $bulkActionType;
    public $confirmingBulkAction;

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
        $this->clearBulkSelection();
    }

    public function toggleBulkSelect()
    {
        $this->bulkSelectMode = !$this->bulkSelectMode;
        $this->clearBulkSelection();
    }

    public function clearBulkSelection()
    {
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Get current page user IDs
            $currentPageUsers = $this->getCurrentPageUsers();
            $this->selectedUsers = array_unique(array_merge($this->selectedUsers, $currentPageUsers));
        } else {
            // Remove current page user IDs
            $currentPageUsers = $this->getCurrentPageUsers();
            $this->selectedUsers = array_diff($this->selectedUsers, $currentPageUsers);
        }
    }

    public function updatedSelectedUsers()
    {
        $currentPageUsers = $this->getCurrentPageUsers();
        $this->selectAll = count(array_intersect($this->selectedUsers, $currentPageUsers)) === count($currentPageUsers);
    }

    private function getCurrentPageUsers()
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

        return $query->paginate($this->perPage)->pluck('id')->toArray();
    }

    public function updatedBulkAction(){
        $this->bulkAction($this->bulkAction);
    }

    public function bulkAction($action)
    {
        if (empty($this->selectedUsers)) {
            $this->dispatch('swal', [
                'title' => 'No Selection',
                'text' => 'Please select at least one user.',
                'icon' => 'warning'
            ]);
            return;
        }

        switch($action){
            case 'email':
                $this->openBulkEmailModal = true;
                break;
            case 'reject':
                $this->bulkActionType = 'reject';
                $this->confirmingBulkAction = true;
                break;
            case 'approve':
                $this->bulkActionType = 'approve';
                $this->confirmingBulkAction = true;
                break;
            case 'deactivate':
                $this->bulkActionType = 'deactivate';
                $this->confirmingBulkAction = true;
                break;
            default:
                break;
        }
    }

    public function confirmAction($userId, $actionType)
    {
        $this->resetBulkAction();
        $this->userId = $userId;
        $this->actionType = $actionType;
        if($actionType == 'email'){
            $this->openEmailModal = true;
        }else{
            $this->confirmingAction = true;
        }
    }

    public function updateStatus()
    {
        if ($this->actionType === 'reject') {
            // Delete the user and their data when rejected
            $user = User::findOrFail($this->userId);
            $user->userData()->delete();
            $user->delete();
            session()->flash('success', 'User has been rejected and removed from the system.');
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
                session()->flash('success', $this->getSuccessMessage($status));
            }
        }

        $this->resetAction();
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedUsers)) {
            $this->dispatch('swal', [
                'title' => 'No Selection',
                'text' => 'Please select at least one user.',
                'icon' => 'warning'
            ]);
            return;
        }

        $selectedCount = count($this->selectedUsers);
        $successCount = 0;
        $errorCount = 0;

        try {
            foreach ($this->selectedUsers as $userId) {
                try {
                    $user = User::findOrFail($userId);
                    
                    switch ($this->bulkActionType) {
                        case 'reject':
                            // Delete the user and their data when rejected
                            $user->userData()->delete();
                            $user->delete();
                            $successCount++;
                            break;
                            
                        case 'approve':
                            $user->update(['active_status' => 1]);
                            $successCount++;
                            break;
                            
                        case 'deactivate':
                            $user->update(['active_status' => 3]);
                            $successCount++;
                            break;
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    Log::error("Failed to {$this->bulkActionType} user ID {$userId}: " . $e->getMessage());
                }
            }

            if ($errorCount === 0) {
                session()->flash('success', $this->getBulkSuccessMessage($this->bulkActionType, $successCount));
            } else {
                session()->flash('warning', "Successfully processed {$successCount} users. {$errorCount} failed.");
            }
            
        } catch (Exception $e) {
            session()->flash('error', 'There was an error processing the bulk action. Please try again.');
            Log::error("Bulk action failed: " . $e->getMessage());
        }
        $this->resetBulkAction();
    }

    private function getBulkSuccessMessage($actionType, $count)
    {
        return match($actionType) {
            'approve' => "Successfully approved {$count} user(s).",
            'reject' => "Successfully rejected and removed {$count} user(s) from the system.",
            'deactivate' => "Successfully deactivated {$count} user(s).",
            default => "Successfully processed {$count} user(s)."
        };
    }

    private function resetBulkAction()
    {
        $this->confirmingBulkAction = false;
        $this->bulkActionType = null;
        $this->clearBulkSelection();
        $this->bulkSelectMode = false;
        $this->bulkAction = '';
    }

    public function resetAction()
    {
        $this->confirmingAction = false;
        $this->openEmailModal = false;
        $this->openBulkEmailModal = false;
        $this->userId = null;
        $this->actionType = '';
        $this->email_subject = '';
        $this->bulkAction = '';
        $this->resetValidation();
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

    public function sendEmail(){
        $this->validate([
            'email_subject' => 'required'
        ]);
        try{
            $userInfo = User::where('id', $this->userId)->first();
    
            $emailTemplate = EmailTemplate::where('name', $this->email_subject)->first();
            if($emailTemplate && $emailTemplate->is_active){
                switch($this->email_subject){
                    case 'agency_submission_reminder_notif':
                        Mail::to( $userInfo ? $userInfo->email : 'test@gmail.com')->send(new SubmissionReminderNotif( Auth::user()->email, $this->email_subject));
                        break;
                }
            }else{
                session()->flash('error', 'Email template is not active.');
                $this->openEmailModal = false;
                return;
            }

            session()->flash('success', 'Email has been successfully sent to the agency representative.');
            $this->resetAction();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function sendBulkEmail()
    {
        $this->validate([
            'email_subject' => 'required'
        ]);

        try {
            $selectedUserIds = $this->selectedUsers;
            $emailSubject = $this->email_subject;
            $senderEmail = Auth::user()->email;

            $emailTemplate = EmailTemplate::where('name', $emailSubject)->first();
            if($emailTemplate && $emailTemplate->is_active){
                SendBulkEmailJob::dispatch(
                    $selectedUserIds, 
                    $emailSubject, 
                    $senderEmail
                );
            }else{
                session()->flash('error', 'Email template is not active.');
                $this->openBulkEmailModal = false;
                return;
            }
            session()->flash('success', 'Email has been successfully sent to the agency representative.');


            $this->resetAction();
            $this->clearBulkSelection();
            $this->bulkSelectMode = false;

        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'There was an error queuing the emails. Please try again.',
                'icon' => 'error'
            ]);
            throw $e;
        }
    }
}