<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use NotificationChannels\WebPush\WebPushMessage;

class TaskDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $taskId;
    protected $whenLabel;

    public function __construct($task, $whenLabel)
    {
        $this->taskId = $task->id;
        $this->whenLabel = $whenLabel;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast']; // add broadcast
    }

    public function toMail($notifiable)
    {
        $task = \App\Models\Task::find($this->taskId);

        return (new MailMessage)
            ->mailer('notification') 
            ->subject("Task due {$this->whenLabel}: {$task->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your task \"{$task->title}\" is due in {$this->whenLabel}.")
            ->action('View Task', url("/tasks/{$task->id}"))
            ->line('Please complete or update the task.');
    }

    public function toDatabase($notifiable)
    {
        $task = \App\Models\Task::find($this->taskId);

        return [
            'task_id' => $task->id,
            'title' => $task->title,
            'due_at' => $task->due_at,
            'when' => $this->whenLabel,
        ];
    }

    // Real-time broadcasting payload
    public function toBroadcast($notifiable)
    {
        $task = \App\Models\Task::find($this->taskId);

        return new BroadcastMessage([
            'task_id' => $task->id,
            'title' => $task->title,
            'due_at' => $task->due_at,
            'when' => $this->whenLabel,
            'message' => "Your task \"{$task->title}\" is due in {$this->whenLabel}!"
        ]);
    }
}
