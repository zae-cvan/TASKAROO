<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskDeadlineNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendTaskDeadlineNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notify:task-deadlines';

    /**
     * The console command description.
     */
    protected $description = 'Send notifications for tasks due soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $checks = [
            '1 day' => $now->copy()->addDay(),
            '1 hour' => $now->copy()->addHour(),
            '30 minutes' => $now->copy()->addMinutes(30),
        ];

        foreach ($checks as $label => $target) {
            $from = $target->copy()->subSeconds(30);
            $to = $target->copy()->addSeconds(30);

            $tasks = Task::whereBetween('due_date', [$from, $to])
                ->whereNull('deleted_at')
                ->get();

            foreach ($tasks as $task) {
                Notification::send([$task->user], new TaskDeadlineNotification($task, $label));
                $this->info("Notification sent for task #{$task->id} ({$label})");
            }
        }

        $this->info('Task deadline notifications check completed.');
    }
}
