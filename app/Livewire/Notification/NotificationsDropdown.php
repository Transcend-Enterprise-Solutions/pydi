<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as NotificationModel;
use App\Models\User;

class NotificationsDropdown extends Component
{
    public $notifications;
    public $unreadCount;
    public $registrationCount = 0;

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $user = Auth::user();
        $query = NotificationModel::where('read', false)
            ->latest();

        if ($user->user_role === 'sa' || $user->user_role === 'admin') {
            // Get existing notifications
            $this->registrationCount = User::where('active_status', 0)->count();

            $notifications = $query->where(function ($q) {
                $q->where('type', 'request')
                    ->orWhere('type', 'registration');
            })->get();

            $this->notifications = $notifications;
            $this->unreadCount = $notifications->count();
        } else {
            // Non-'sa' users see only their own notifications, excluding 'request' type
            $notifications = $query->where('user_id', $user->id)
                ->where('type', '!=', 'request')
                ->get();

            $this->notifications = $notifications->groupBy('type')
                ->map(function ($group) {
                    return [
                        'type' => $group->first()->type,
                        'count' => $group->count(),
                        'latest' => $group->first(),
                        'ids' => $group->pluck('id')->toArray(),
                    ];
                });
            $this->unreadCount = $notifications->count();
        }
    }

    public function markGroupAsRead($type)
    {
        $user = Auth::user();
        $query = NotificationModel::where('type', $type)
            ->where('read', false);

        if ($user->user_role === 'sa' || $user->user_role === 'admin') {
            $query->where('type', 'request')
                ->orWhere('type', 'registration');
        } else {
            $query->where('user_id', $user->id);
        }

        $query->update(['read' => true]);
        $this->refreshNotifications();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $query = NotificationModel::where('read', false);

        if ($user->user_role === 'sa' || $user->user_role === 'admin') {
            $query->where('type', 'request')
                ->orWhere('type', 'registration');
        } else {
            $query->where('user_id', $user->id);
        }

        $query->update(['read' => true]);
        $this->refreshNotifications();
    }

    private function getDocumentTypeLabel($documentType)
    {
        $documentTypes = [
            'employment' => 'Certificate of Employment',
            'employmentCompensation' => 'Certificate of Employment with Compensation',
            'leaveCredits' => 'Certificate of Leave Credits',
            'ipcrRatings' => 'Certificate of IPCR Ratings',
        ];
        return $documentTypes[$documentType] ?? $documentType;
    }

    public function render()
    {
        if (Auth::user()->user_role === 'sa') {
            return view('livewire.notification.notifications-dropdown', [
                'notifications' => $this->notifications,
                'unreadCount' => $this->unreadCount,
            ]);
        } else {
            return view('livewire.notification.notifications-dropdown', [
                'groupedNotifications' => $this->notifications,
                'unreadCount' => $this->unreadCount,
            ]);
        }
    }

    // Add method to get notification message
    private function getRegistrationMessage()
    {
        if ($this->registrationCount === 1) {
            return '1 new registration pending approval';
        }
        return ($this->registrationCount) . ' pending registration approval';
    }
}
