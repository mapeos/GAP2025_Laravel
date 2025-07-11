<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;
    public $greeting;
    public $body;
    public $actionText;
    public $actionUrl;
    public $footerText;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $subject,
        string $greeting = 'Hello!',
        string $body = '',
        ?string $actionText = null,
        ?string $actionUrl = null,
        string $footerText = 'Thank you for using our application!',
        $recipient = null
    ) {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->body = $body;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->footerText = $footerText;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            subject: $this->subject,
        );

        // Set recipient if provided
        if ($this->recipient) {
            $envelope->to($this->recipient);
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-notification',
            with: [
                'subject' => $this->subject,
                'greeting' => $this->greeting,
                'body' => $this->body,
                'actionText' => $this->actionText,
                'actionUrl' => $this->actionUrl,
                'footerText' => $this->footerText,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
