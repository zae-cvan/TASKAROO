<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $taskId;

    public function __construct($message, $taskId = null)
    {
        $this->message = $message;
        $this->taskId = $taskId;
    }

    public function via($notifiable)
    {
        return ['database']; // pwede rin 'mail', 'broadcast', etc.
    }

    public function toDatabase($notifiable)
    {
        $data = [
            'message' => $this->message,
            'title' => $this->message,
        ];
        
        if ($this->taskId) {
            $data['task_id'] = $this->taskId;
        }
        
        return $data;
    }

    public function toArray($notifiable)
    {
        $data = [
            'message' => $this->message,
            'title' => $this->message,
        ];
        
        if ($this->taskId) {
            $data['task_id'] = $this->taskId;
        }
        
        return $data;
    }
}
