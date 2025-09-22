<?php

namespace App\Jobs;

use App\Models\User;
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
    public $timeout = 600; // Increased timeout for bulk operations

    /**
     * Create a new job instance.
     */
    public function __construct(array $userIds, string $template, string $senderEmail, $isAccountAction = false)
    {
        $this->userIds = $userIds;
        $this->template = $template;
        $this->senderEmail = $senderEmail;
        $this->isAccountAction = $isAccountAction;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        try {
            $users = User::whereIn('id', $this->userIds)->get();
            
            if ($users->isEmpty()) {
                Log::warning("No users found for bulk email job", ['user_ids' => $this->userIds]);
                return;
            }

            Log::info("Starting bulk email job", [
                'template' => $this->template,
                'user_count' => $users->count(),
                'sender' => $this->senderEmail
            ]);

            $successCount = 0;
            $failedCount = 0;
            $delaySeconds = 0;
            
            foreach ($users as $user) {
                try {
                    // Use EmailService with individual error handling
                    $result = $emailService->sendEmail(
                        $user->email,
                        $this->template,
                        $this->createMailable($user)
                    );

                    if ($result['success']) {
                        $successCount++;
                        Log::debug("Email sent successfully", [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'template' => $this->template
                        ]);
                    } else {
                        $failedCount++;
                        Log::warning("Email failed to send", [
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'template' => $this->template,
                            'error' => $result['message']
                        ]);
                    }

                    // Add delay between emails to avoid rate limiting
                    if ($delaySeconds < count($this->userIds) - 1) {
                        sleep(2);
                        $delaySeconds++;
                    }

                } catch (Exception $e) {
                    $failedCount++;
                    Log::error("Exception in bulk email job for user", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            Log::info("Bulk email job completed", [
                'template' => $this->template,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'total_users' => $users->count()
            ]);
            
        } catch (Exception $e) {
            Log::error("Bulk email job failed completely", [
                'template' => $this->template,
                'user_ids' => $this->userIds,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create the appropriate mailable instance
     */
    private function createMailable(User $user)
    {
        if ($this->isAccountAction) {
            return new UserRegistrationNotif($this->senderEmail, $this->template);
        } else {
            return new \App\Mail\SubmissionReminderNotif($this->senderEmail, $this->template);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("Bulk email job failed permanently", [
            'user_ids' => $this->userIds,
            'template' => $this->template,
            'sender_email' => $this->senderEmail,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}