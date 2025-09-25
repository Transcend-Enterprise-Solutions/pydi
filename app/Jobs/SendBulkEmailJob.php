<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserLog;
use App\Services\EmailService;
use App\Mail\UserRegistrationNotif;
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
    protected $template;
    protected $senderEmail;
    protected $isAccountAction;
    protected $initiatedByUserId; // Store the user who initiated this

    public $tries = 3;
    public $timeout = 600;

    public function __construct(array $userIds, string $template, string $senderEmail, $isAccountAction = false)
    {
        $this->userIds = $userIds;
        $this->template = $template;
        $this->senderEmail = $senderEmail;
        $this->isAccountAction = $isAccountAction;
        $this->initiatedByUserId = auth()->id();
    }

    public function handle(EmailService $emailService): void
    {
        try {
            $users = User::whereIn('id', $this->userIds)->get();
            
            if ($users->isEmpty()) {
                $this->logFailure('No users found for bulk email job');
                return;
            }

            Log::info("Starting bulk email job", [
                'template' => $this->template,
                'user_count' => $users->count(),
                'sender' => $this->senderEmail,
                'initiated_by' => $this->initiatedByUserId
            ]);

            $successCount = 0;
            $failedCount = 0;
            $failedEmails = [];
            
            foreach ($users as $user) {
                try {
                    $result = $emailService->sendEmail(
                        $user->email,
                        $this->template,
                        $this->createMailable($user)
                    );

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $failedEmails[] = [
                            'email' => $user->email,
                            'error' => $result['message']
                        ];
                    }

                    // Add delay between emails to avoid rate limits
                    sleep(2);

                } catch (Exception $e) {
                    $failedCount++;
                    $failedEmails[] = [
                        'email' => $user->email,
                        'error' => 'Exception: ' . $e->getMessage()
                    ];

                    Log::error("Exception in bulk email job for user", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log bulk email completion summary
            $this->logCompletion($successCount, $failedCount, $failedEmails, $users->count());
            
        } catch (Exception $e) {
            $this->logFailure($e->getMessage());
            throw $e;
        }
    }

    private function createMailable(User $user)
    {
        if ($this->isAccountAction) {
            return new UserRegistrationNotif($this->senderEmail, $this->template);
        } else {
            return new \App\Mail\SubmissionReminderNotif($this->senderEmail, $this->template);
        }
    }

    private function logCompletion(int $successCount, int $failedCount, array $failedEmails, int $totalUsers)
    {
        try {
            if ($failedCount > 0) {
                $action = "Bulk Email Job Completed: {$this->template}. {$successCount}/{$totalUsers} sent successfully. {$failedCount} failed.";
                
                if (!empty($failedEmails)) {
                    $failedEmailsList = collect($failedEmails)->take(3)->pluck('email')->implode(', ');
                    $action .= " Failed emails: {$failedEmailsList}";
                    if (count($failedEmails) > 3) {
                        $action .= " and " . (count($failedEmails) - 3) . " more.";
                    }
                }
            } else {
                $action = "Bulk Email Job Success: {$this->template}. All {$successCount} emails sent successfully.";
            }

            UserLog::create([
                'user_id' => $this->initiatedByUserId,
                'action' => $action,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("Bulk email job completed", [
                'template' => $this->template,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'total_users' => $totalUsers,
                'initiated_by' => $this->initiatedByUserId
            ]);

        } catch (Exception $e) {
            Log::error("Failed to log bulk email completion", [
                'error' => $e->getMessage(),
                'template' => $this->template,
                'success_count' => $successCount,
                'failed_count' => $failedCount
            ]);
        }
    }

    private function logFailure(string $error)
    {
        try {
            UserLog::create([
                'user_id' => $this->initiatedByUserId,
                'action' => "Bulk Email Job Failed: {$this->template}. Error: {$error}",
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::error("Bulk email job failed completely", [
                'template' => $this->template,
                'user_ids' => $this->userIds,
                'error' => $error,
                'initiated_by' => $this->initiatedByUserId
            ]);

        } catch (Exception $e) {
            Log::error("Failed to log bulk email job failure", [
                'original_error' => $error,
                'logging_error' => $e->getMessage()
            ]);
        }
    }

    public function failed(Exception $exception): void
    {
        $this->logFailure("Job failed permanently: " . $exception->getMessage());
    }
}