<?php

namespace App\Jobs;

use App\Services\EmailService;
use App\Mail\SubmissionReminderNotif;
use App\Mail\UserRegistrationNotif;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $adminEmail;
    protected $template;
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
    public $timeout = 120;

    public function __construct($email, $adminEmail, $template, $isAccountAction = false)
    {
        $this->email = $email;
        $this->adminEmail = $adminEmail;
        $this->template = $template;
        $this->isAccountAction = $isAccountAction;
    }

    public function handle(EmailService $emailService): void
    {
        try {
            $mailable = $this->createMailable();
            
            $result = $emailService->sendEmail(
                $this->email,
                $this->template,
                $mailable
            );

            if ($result['success']) {
                Log::info("Single email sent successfully", [
                    'email' => $this->email,
                    'template' => $this->template,
                    'is_account_action' => $this->isAccountAction
                ]);
            } else {
                Log::warning("Single email failed to send", [
                    'email' => $this->email,
                    'template' => $this->template,
                    'error' => $result['message']
                ]);
                
                // Mark job as failed if email couldn't be sent
                $this->fail(new \Exception($result['message']));
            }

        } catch (\Exception $e) {
            Log::error("Exception in single email job", [
                'email' => $this->email,
                'template' => $this->template,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create the appropriate mailable instance
     */
    private function createMailable()
    {
        if ($this->isAccountAction) {
            return new UserRegistrationNotif($this->adminEmail, $this->template);
        } else {
            return new SubmissionReminderNotif($this->adminEmail, $this->template);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Single email job failed permanently", [
            'email' => $this->email,
            'template' => $this->template,
            'admin_email' => $this->adminEmail,
            'is_account_action' => $this->isAccountAction,
            'error' => $exception->getMessage()
        ]);
    }
}