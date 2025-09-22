<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\Mailer\Exception\TransportException;

class EmailService
{
    /**
     * Send email with error handling
     *
     * @param string $to
     * @param string $templateName
     * @param mixed $mailable
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendEmail(string $to, string $templateName, $mailable, array $data = []): array
    {
        try {
            // Check if template exists and is active
            $emailTemplate = EmailTemplate::where('name', $templateName)->first();
            
            if (!$emailTemplate || !$emailTemplate->is_active) {
                return [
                    'success' => false,
                    'message' => 'Email template is not active or does not exist.'
                ];
            }

            // Attempt to send email
            Mail::to($to)->send($mailable);

            // Log successful email
            Log::info("Email sent successfully", [
                'template' => $templateName,
                'recipient' => $to
            ]);

            return [
                'success' => true,
                'message' => 'Email sent successfully.'
            ];

        } catch (TransportException $e) {
            return $this->handleTransportException($e, $templateName, $to);
        } catch (Exception $e) {
            return $this->handleSwiftTransportException($e, $templateName, $to);
        } catch (Exception $e) {
            return $this->handleGeneralException($e, $templateName, $to);
        }
    }

    /**
     * Handle Symfony Transport exceptions (newer Laravel versions)
     */
    private function handleTransportException(TransportException $e, string $templateName, string $to): array
    {
        $message = $e->getMessage();
        
        // SMTP Authentication errors
        if (str_contains($message, '535')) {
            $errorMessage = 'Email not sent. Email authentication failed. Please check SMTP credentials.';
        }
        // Connection errors
        elseif (str_contains($message, 'Connection') || str_contains($message, 'timeout')) {
            $errorMessage = 'Email not sent. Unable to connect to email server. Please try again later.';
        }
        // Rate limit errors
        elseif (str_contains($message, 'rate limit') || str_contains($message, 'quota')) {
            $errorMessage = 'Email not sent. Email sending limit reached. Please try again later.';
        }
        // Generic SMTP errors
        else {
            $errorMessage = 'Email not sent. Failed to send email due to server configuration issue.';
        }

        Log::error("Email sending failed - Transport Exception", [
            'template' => $templateName,
            'recipient' => $to,
            'error' => $message
        ]);

        return [
            'success' => false,
            'message' => $errorMessage
        ];
    }

    /**
     * Handle Swift Transport exceptions (older Laravel versions)
     */
    private function handleSwiftTransportException(Exception $e, string $templateName, string $to): array
    {
        $message = $e->getMessage();
        
        // SMTP Authentication errors
        if (str_contains($message, '535') || str_contains($message, 'Username and Password not accepted')) {
            $errorMessage = 'Email not sent. Email authentication failed. Please check SMTP credentials.';
        }
        // Connection errors
        elseif (str_contains($message, 'Connection') || str_contains($message, 'timeout')) {
            $errorMessage = 'Email not sent. Unable to connect to email server. Please try again later.';
        }
        // Rate limit errors
        elseif (str_contains($message, 'rate limit') || str_contains($message, 'quota')) {
            $errorMessage = 'Email not sent. Email sending limit reached. Please try again later.';
        }
        // Generic SMTP errors
        else {
            $errorMessage = 'Email not sent. Failed to send email due to server configuration issue.';
        }

        Log::error("Email sending failed - Swift Transport Exception", [
            'template' => $templateName,
            'recipient' => $to,
            'error' => $message
        ]);

        return [
            'success' => false,
            'message' => $errorMessage
        ];
    }

    /**
     * Handle general exceptions
     */
    private function handleGeneralException(Exception $e, string $templateName, string $to): array
    {
        Log::error("Email sending failed - General Exception", [
            'template' => $templateName,
            'recipient' => $to,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'success' => false,
            'message' => 'An unexpected error occurred while sending email.'
        ];
    }

    /**
     * Send bulk emails with individual error handling
     *
     * @param array $recipients
     * @param string $templateName
     * @param callable $mailableCallback
     * @param bool $useQueue Whether to use queue for bulk sending
     * @return array ['sent' => int, 'failed' => int, 'errors' => array]
     */
    public function sendBulkEmails(array $recipients, string $templateName, callable $mailableCallback, bool $useQueue = false): array
    {
        // Check template first
        $emailTemplate = EmailTemplate::where('name', $templateName)->first();
        
        if (!$emailTemplate || !$emailTemplate->is_active) {
            return [
                'sent' => 0,
                'failed' => count($recipients),
                'errors' => [['email' => 'all', 'error' => 'Email template is not active or does not exist.']]
            ];
        }

        if ($useQueue && count($recipients) > 1) {
            return $this->queueBulkEmails($recipients, $templateName, $mailableCallback);
        } else {
            return $this->sendBulkEmailsImmediately($recipients, $templateName, $mailableCallback);
        }
    }

    /**
     * Send bulk emails immediately (synchronous)
     */
    private function sendBulkEmailsImmediately(array $recipients, string $templateName, callable $mailableCallback): array
    {
        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($recipients as $recipient) {
            $mailable = $mailableCallback($recipient);
            $result = $this->sendEmail($recipient['email'], $templateName, $mailable);
            
            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                $errors[] = [
                    'email' => $recipient['email'],
                    'error' => $result['message']
                ];
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    /**
     * Queue bulk emails for background processing
     */
    private function queueBulkEmails(array $recipients, string $templateName, callable $mailableCallback): array
    {
        try {
            // Extract user IDs and emails for the job
            $userIds = array_map(function($recipient) {
                return $recipient['user']->id ?? null;
            }, $recipients);

            $userIds = array_filter($userIds); // Remove nulls

            if (empty($userIds)) {
                return [
                    'sent' => 0,
                    'failed' => count($recipients),
                    'errors' => [['email' => 'all', 'error' => 'No valid user IDs found for queuing.']]
                ];
            }

            // Dispatch the job
            \App\Jobs\SendBulkEmailJob::dispatch(
                $userIds,
                $templateName,
                \Illuminate\Support\Facades\Auth::user()->email,
                true
            );

            Log::info("Bulk email job queued", [
                'template' => $templateName,
                'recipient_count' => count($userIds)
            ]);

            return [
                'sent' => count($userIds),
                'failed' => 0,
                'errors' => [],
                'queued' => true
            ];

        } catch (Exception $e) {
            Log::error("Failed to queue bulk emails", [
                'template' => $templateName,
                'recipient_count' => count($recipients),
                'error' => $e->getMessage()
            ]);

            return [
                'sent' => 0,
                'failed' => count($recipients),
                'errors' => [['email' => 'all', 'error' => 'Failed to queue bulk emails: ' . $e->getMessage()]]
            ];
        }
    }
}