<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Bell and email message about a request: either "your action is needed" or "status changed". */
class RequestUpdated extends Notification
{
    public function __construct(
        public string $requestNo,
        public string $status,
        public string $title,
        public string $line,
        public string $url,
        public bool $actionNeeded,
    ) {
    }

    public function via(object $notifiable): array
    {
        return $notifiable->email ? ['database', 'mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(($this->actionNeeded ? 'Action needed: ' : 'Update: ') . $this->requestNo . ' - ' . $this->status)
            ->greeting('Hello ' . (explode(' ', trim($notifiable->name))[0] ?? ''))
            ->line($this->title)
            ->line($this->line)
            ->action('Open request', $this->url)
            ->salutation('VIVA Branding & Events Portal');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_no' => $this->requestNo,
            'status' => $this->status,
            'title' => $this->title,
            'line' => $this->line,
            'url' => $this->url,
            'action_needed' => $this->actionNeeded,
        ];
    }
}
