<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $emailSubject;
    protected $senderEmail;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(array $userIds, string $emailSubject, string $senderEmail)
    {
        $this->userIds = $userIds;
        $this->emailSubject = $emailSubject;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $users = User::whereIn('id', $this->userIds)->get();
            
            $successCount = 0;
            $failedCount = 0;
            
            foreach ($users as $index => $user) {
                try {
                    $this->sendEmailToUser($user, $this->senderEmail, $index);
                    $successCount++;
                } catch (Exception $e) {
                    $failedCount++;
                    Log::error("Failed to send bulk email to user ID {$user->id}: " . $e->getMessage());
                }
            }
            
            Log::info("Bulk email job completed. Success: {$successCount}, Failed: {$failedCount}");
            
        } catch (Exception $e) {
            Log::error("Bulk email job failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send email to individual user based on email subject
     */
    private function sendEmailToUser(User $user, $senderEmail, $index): void
    {
        $recipientEmail = $user->email ?? 'test@gmail.com';
        
        switch ($this->emailSubject) {
            case 'agency_submission_reminder_notif':
                SendSingleEmailJob::dispatch(
                    $recipientEmail, 
                    $senderEmail,
                    $this->emailSubject,
                    )->delay(now()->addSeconds($index * 2));
                break;
            default:
                throw new Exception("Unknown email subject: {$this->emailSubject}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("Bulk email job failed permanently: " . $exception->getMessage(), [
            'user_ids' => $this->userIds,
            'email_subject' => $this->emailSubject,
            'sender_email' => $this->senderEmail
        ]);
    }
}