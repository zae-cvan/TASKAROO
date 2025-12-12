<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class AutoArchiveTasks extends Command
{
    protected $signature = 'tasks:auto-archive';
    protected $description = 'Automatically archive overdue tasks';

    public function handle()
    {
        $count = Task::where('status', 'active')
            ->where('due_date', '<', now())
            ->update(['status' => 'archived']);

        $this->info("Archived {$count} overdue tasks.");
    }
}
