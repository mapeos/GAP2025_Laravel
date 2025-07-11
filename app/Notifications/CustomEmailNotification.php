<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subject;
    public $greeting;
    public $body;
    public $actionText;
    public $actionUrl;
    public $footerText;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $subject,
        string $greeting = 'Hello!',
        string $body = '',
        ?string $actionText = null,
        ?string $actionUrl = null,
        string $footerText = 'Thank you for using our application!'
    ) {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->body = $body;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->footerText = $footerText;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->greeting)
            ->line($this->body);

        // Add action button if provided
        if ($this->actionText && $this->actionUrl) {
            $mailMessage->action($this->actionText, $this->actionUrl);
        }

        // Add footer text
        if ($this->footerText) {
            $mailMessage->line($this->footerText);
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'body' => $this->body,
            'action_text' => $this->actionText,
            'action_url' => $this->actionUrl,
            'footer_text' => $this->footerText,
        ];
    }
}
