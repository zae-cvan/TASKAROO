<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;
use App\Notifications\TaskNotification;

class TaskAssignmentController extends Controller
{
    // Show list of tasks and users to assign
    public function index()
    {
        // Only show tasks that the current admin created (admin-assigned tasks)
        // Show tasks either unassigned or created by this admin for assignment
        $tasks = Task::where(function($q) {
            $q->whereNull('user_id');
        })->orWhere('created_by', Auth::id())->latest()->get();

        // Exclude admin users and only show active users to assign
        $users = User::where('role', '!=', 'admin')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.tasks.assign', compact('tasks','users'));
    }

    // Assign an existing task to a user
    public function assign(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::findOrFail($request->task_id);
        // Do not allow assigning to admin accounts or to self
        $target = User::findOrFail($request->user_id);
        if ($target->role === 'admin' || $target->id === Auth::id()) {
            return back()->withErrors(['user_id' => 'Cannot assign tasks to admin accounts or yourself.']);
        }

        $task->user_id = $request->user_id;
        if (Schema::hasColumn('tasks', 'created_by') && ! $task->created_by) {
            $task->created_by = Auth::id();
        }
        $task->category = 'Task from Admin';
        $task->status = $task->status ?? 'active';
        $task->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Assigned task to user',
            'details' => "Task ID: {$task->id} assigned to User ID: {$request->user_id}"
        ]);

        // Notify the assigned user
        try {
            $task->user->notify(new TaskNotification("You were assigned a task: {$task->title}"));
        } catch (\Throwable $e) {
            // don't break flow if notification fails
        }

        return back()->with('success', 'Task assigned!');
    }

    // Create & assign new task to user
    public function storeAndAssign(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'urgency' => 'required|in:very_urgent,urgent,normal,least_urgent',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt|max:5120'
        ]);

        // Prevent assigning to admin accounts or to self
        $target = User::findOrFail($request->user_id);
        if ($target->role === 'admin' || $target->id === Auth::id()) {
            return back()->withErrors(['user_id' => 'Cannot assign tasks to admin accounts or yourself.']);
        }

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->user_id = $request->user_id;
        if (Schema::hasColumn('tasks', 'created_by')) {
            $task->created_by = Auth::id();
        }
        $task->category = 'Task from Admin';
        $task->urgency = $request->urgency;
        $levels = config('urgency.levels');
        $task->color = $levels[$task->urgency]['color'] ?? null;
        $task->status = 'active';

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            try {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;
            } catch (\Throwable $e) {}
        }
        $task->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created & assigned task',
            'details' => 'Task ID: '.$task->id
        ]);

        // Notify the assigned user
        try {
            $task->user->notify(new TaskNotification("You were assigned a task: {$task->title}"));
        } catch (\Throwable $e) {
            // ignore notification errors
        }

        return back()->with('success', 'Task created and assigned.');
    }
}
