<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationNotif extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $adminEmail;
    protected $templateName;

    public function __construct($adminEmail, $templateName)
    {
        $this->adminEmail = $adminEmail;
        $this->templateName = $templateName;
    }

    public function build()
    {
        $template = EmailTemplate::getByName($this->templateName);
        
        if (!$template) {
            throw new \Exception("Email template '{$this->templateName}' not found.");
        }

        // Replace placeholders in message body
        // $messageBody = $template->replacePlaceholders($this->emailBody);

        return $this->from($this->adminEmail)
            ->view('livewire.emails.email-template')
            ->subject($template->subject)
            ->with([
                'header' => $template->header,
                'greetings' => $template->greetings,
                'message_body' => $template->message_body,
                'notifDetails' => null,
                'isAdmin' => true,
                'footer' => $template->footer,
                'action_button_text' => $template->action_button_text,
                'action_button_url' => $template->action_button_url,
            ]);
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::getByName($this->templateName);
        $subject = $template ? $template->subject : 'PYDI Email Notif';
        
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'livewire.emails.email-template',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}