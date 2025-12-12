<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function updated(Task $task)
    {
        // Observer logic removed - tasks should remain in their set status
    }
}
