<?php

namespace App\Livewire\Admin;

use App\Mail\SubmissionReminderNotif;
use App\Mail\UserRegistrationNotif;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Services\EmailService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    protected EmailService $emailService;

    public function __construct()
    {
        $this->emailService = app(EmailService::class);
    }

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
                $this->bulkActionType = 'email';
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
            $user = User::findOrFail($this->userId);
            $user->userData()->delete();
            $user->delete();

            // Send rejection email with error handling
            $result = $this->emailService->sendEmail(
                $user->email,
                'registration_rejection_notif',
                new UserRegistrationNotif(Auth::user()->email, 'registration_rejection_notif')
            );

            if ($result['success']) {
                session()->flash('success', 'User has been rejected and removed from the system.');
            } else {
                session()->flash('success', 'User has been rejected and removed from the system.');
                session()->flash('error', $result['message']);
            }
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

                $successMessage = $this->getSuccessMessage($status);

                $templateName = match($status) {
                    1 => 'registration_approval_notif',
                    3 => 'account_deactivation_notif',
                    default => null
                };

                if ($templateName) {
                    $result = $this->emailService->sendEmail(
                        $user->email,
                        $templateName,
                        new UserRegistrationNotif(Auth::user()->email, $templateName)
                    );

                    if ($result['success']) {
                        session()->flash('success', $successMessage);
                    } else {
                        session()->flash('success', $successMessage);
                        session()->flash('error', $result['message']);
                    }
                } else {
                    session()->flash('success', $successMessage);
                }
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
        $processedUsers = [];

        try {
            foreach ($this->selectedUsers as $userId) {
                try {
                    $user = User::findOrFail($userId);
                    
                    switch ($this->bulkActionType) {
                        case 'reject':
                            $user->userData()->delete();
                            $user->delete();
                            $successCount++;
                            $processedUsers[] = $user;
                            break;
                            
                        case 'approve':
                            $user->update(['active_status' => 1]);
                            $successCount++;
                            $processedUsers[] = $user;
                            break;
                            
                        case 'deactivate':
                            $user->update(['active_status' => 3]);
                            $successCount++;
                            $processedUsers[] = $user;
                            break;
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    Log::error("Failed to {$this->bulkActionType} user ID {$userId}: " . $e->getMessage());
                }
            }

            if (!empty($processedUsers)) {
                $this->handleBulkEmails($processedUsers);
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

    private function handleBulkEmails(array $users)
    {
        $templateName = $this->getBulkEmailTemplate();
        
        if (!$templateName) {
            return;
        }

        $recipients = array_map(function($user) {
            return [
                'email' => $user->email,
                'user' => $user
            ];
        }, $users);

        // Determine if we should use queue (for more than 10 users)
        $useQueue = count($users) > 1;

        // Send bulk emails using EmailService
        $result = $this->emailService->sendBulkEmails(
            $recipients,
            $templateName,
            function($recipient) use ($templateName) {
                return new UserRegistrationNotif(Auth::user()->email, $templateName);
            },
            $useQueue
        );

        if (isset($result['queued']) && $result['queued']) {
            $this->addEmailQueuedMessage(count($users));
        } elseif ($result['failed'] > 0) {
            $this->addEmailErrorMessage($result);
        }
    }

    private function addEmailQueuedMessage(int $userCount)
    {
        $currentMessage = session()->get('success', '');
        $emailMessage = " Notification emails for {$userCount} users have been queued for background processing.";
        session()->flash('success', $currentMessage . $emailMessage);
    }

    private function addEmailErrorMessage(array $result)
    {
        $errorMessage = "Actions completed successfully, but {$result['failed']} email(s) failed to send.";
        
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                Log::warning("Bulk email failed", $error);
            }
            
            $errorMessages = array_column($result['errors'], 'error');
            $uniqueErrors = array_unique($errorMessages);
            
            if (count($uniqueErrors) === 1) {
                $errorMessage .= " Error: " . $uniqueErrors[0];
            }
        }
        
        session()->flash('warning', $errorMessage);
    }

    private function getBulkEmailTemplate(): ?string
    {
        return match($this->bulkActionType) {
            'reject' => 'registration_rejection_notif',
            'approve' => 'registration_approval_notif',
            'deactivate' => 'account_deactivation_notif',
            default => null
        };
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

    public function sendEmail()
    {
        $this->validate([
            'email_subject' => 'required'
        ]);

        try {
            $userInfo = User::where('id', $this->userId)->first();

            if (!$userInfo) {
                session()->flash('error', 'User not found.');
                $this->openEmailModal = false;
                return;
            }

            $mailable = $this->createSingleEmailMailable($userInfo);
            
            if (!$mailable) {
                session()->flash('error', 'Unsupported email template.');
                $this->openEmailModal = false;
                return;
            }

            // Use EmailService for sending with proper error handling
            $result = $this->emailService->sendEmail(
                $userInfo->email,
                $this->email_subject,
                $mailable
            );

            if ($result['success']) {
                session()->flash('success', 'Email has been successfully sent to the agency representative.');
                $this->resetAction();
            } else {
                session()->flash('error', $result['message']);
                $this->openEmailModal = false;
            }

        } catch (Exception $e) {
            Log::error("Single email send failed", [
                'user_id' => $this->userId,
                'email_subject' => $this->email_subject,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'An unexpected error occurred while sending the email.');
            $this->openEmailModal = false;
        }
    }

    public function sendBulkEmail()
    {
        $this->validate([
            'email_subject' => 'required'
        ]);

        if (empty($this->selectedUsers)) {
            $this->dispatch('swal', [
                'title' => 'No Selection',
                'text' => 'Please select at least one user.',
                'icon' => 'warning'
            ]);
            return;
        }

        try {
            $users = User::whereIn('id', $this->selectedUsers)->get();
            
            if ($users->isEmpty()) {
                session()->flash('error', 'Selected users not found.');
                $this->openBulkEmailModal = false;
                return;
            }

            $recipients = $users->map(function($user) {
                return [
                    'email' => $user->email,
                    'user' => $user
                ];
            })->toArray();

            $useQueue = count($users) > 1;

            $result = $this->emailService->sendBulkEmails(
                $recipients,
                $this->email_subject,
                function($recipient) {
                    return $this->createBulkEmailMailable($recipient['user']);
                },
                $useQueue
            );

            $this->handleBulkEmailResult($result, count($users));
            
            $this->resetAction();
            $this->clearBulkSelection();
            $this->bulkSelectMode = false;

        } catch (Exception $e) {
            Log::error("Bulk email send failed", [
                'selected_users' => $this->selectedUsers,
                'email_subject' => $this->email_subject,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'There was an error processing the bulk email request. Please try again.',
                'icon' => 'error'
            ]);
            
            $this->openBulkEmailModal = false;
        }
    }

    /**
     * Create mailable for single email sending
     */
    private function createSingleEmailMailable(User $user)
    {
        return match($this->email_subject) {
            'agency_submission_reminder_notif' => new SubmissionReminderNotif(Auth::user()->email, $this->email_subject),
            default => null
        };
    }

    /**
     * Create mailable for bulk email sending
     */
    private function createBulkEmailMailable(User $user)
    {
        return match($this->email_subject) {
            'agency_submission_reminder_notif' => new SubmissionReminderNotif(Auth::user()->email, $this->email_subject),
            // Add more email types as needed
            default => new SubmissionReminderNotif(Auth::user()->email, $this->email_subject)
        };
    }

    /**
     * Handle bulk email sending results
     */
    private function handleBulkEmailResult(array $result, int $totalUsers)
    {
        if (isset($result['queued']) && $result['queued']) {
            session()->flash('success', "Emails for {$totalUsers} users have been queued for background processing. You will receive notifications once they are sent.");
        } elseif ($result['failed'] === 0) {
            session()->flash('success', "Emails have been successfully sent to {$result['sent']} agency representatives.");
        } elseif ($result['sent'] > 0) {
            session()->flash('warning', "Emails sent to {$result['sent']} users successfully, but {$result['failed']} failed to send. Please check the logs for details.");
        } else {
            if (!empty($result['errors']) && count($result['errors']) === 1) {
                session()->flash('error', $result['errors'][0]['error']);
            } else {
                session()->flash('error', 'All emails failed to send. Please check your email configuration and try again.');
            }
        }

        $this->openBulkEmailModal = false;
    }
}