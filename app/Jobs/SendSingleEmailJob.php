<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionReminderNotif;
use Illuminate\Support\Facades\Log;

class SendSingleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $adminEmail;
    protected $template;

    public function __construct($email, $adminEmail, $template)
    {
        $this->email = $email;
        $this->adminEmail = $adminEmail;
        $this->template = $template;
    }

    public function handle(): void
    {
        Mail::to($this->email)->queue(new SubmissionReminderNotif($this->adminEmail, $this->template));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send training notification to {$this->email}: " . $exception->getMessage());
    }
}