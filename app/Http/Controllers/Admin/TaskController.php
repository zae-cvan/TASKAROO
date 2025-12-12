<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Show edit form for a task
    // Redirect GET show to edit page for admin convenience
    public function show($id)
    {
        return redirect()->route('admin.tasks.edit', $id);
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        // Provide a list of assignable users (exclude admins)
        $users = \App\Models\User::where('role', '!=', 'admin')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.tasks.edit', compact('task', 'users'));
    }

    // List tasks assigned/created by this admin (with optional status filter)
    public function index(Request $request)
    {
        $adminId = auth()->id();
        $status = $request->query('status', 'active');

        if ($status === 'recycle') {
            $tasks = Task::onlyTrashed()->where('created_by', $adminId)->latest()->get();
        } else {
            $query = Task::where('created_by', $adminId);
            if ($status) {
                $query->where('status', $status);
            }
            $tasks = $query->orderBy('is_pinned', 'desc')->latest()->get();
        }

        return view('admin.tasks.index', compact('tasks', 'status'));
    }

    // Update a task
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => ['required', Rule::in(['active','completed','archived'])],
            'color' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt|max:5120',
        ]);

        // Normalize due_date to a proper datetime if provided
        if (!empty($validated['due_date'])) {
            $validated['due_date'] = date('Y-m-d H:i:s', strtotime($validated['due_date']));
        }

        // Keep old assignee and status to detect changes
        $oldUserId = $task->user_id;
        $oldStatus = $task->status;

        $task->update($validated);

        // Handle attachment replacement if uploaded
        if ($request->hasFile('attachment')) {
            try {
                $path = $request->file('attachment')->store('attachments', 'public');
                // delete old
                if ($task->attachment && Storage::disk('public')->exists($task->attachment)) {
                    Storage::disk('public')->delete($task->attachment);
                }
                $task->attachment = $path;
                $task->save();
            } catch (\Throwable $e) {}
        }

        // Notify if status changed to archived
        try {
            if ($task->status === 'archived' && $oldStatus !== 'archived' && $task->user_id && $task->user_id != auth()->id()) {
                $assignedUser = \App\Models\User::find($task->user_id);
                if ($assignedUser) {
                    $assignedUser->notify(new \App\Notifications\TaskNotification("Admin archived the task assigned to you: {$task->title}", $task->id));
                }
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }

        // If assignee changed, notify new assignee and optionally the previous assignee
        try {
            $newUserId = $task->user_id;
            if ($newUserId && $newUserId != $oldUserId) {
                $newUser = \App\Models\User::find($newUserId);
                if ($newUser) {
                    $newUser->notify(new \App\Notifications\TaskNotification("You were assigned a task: {$task->title}"));
                }
            }

            if ($oldUserId && $oldUserId != ($task->user_id ?? null)) {
                // notify previous assignee that they were unassigned
                $oldUser = \App\Models\User::find($oldUserId);
                if ($oldUser) {
                    $oldUser->notify(new \App\Notifications\TaskNotification("You were unassigned from task: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {
            // swallow notification errors
        }

        return redirect()->route('admin.dashboard')->with('success', 'Task updated successfully.');
    }

    // Delete a task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        // Notify the assigned user if task was assigned to someone
        try {
            if ($task->user_id && $task->user_id != auth()->id()) {
                $assignedUser = \App\Models\User::find($task->user_id);
                if ($assignedUser) {
                    $assignedUser->notify(new \App\Notifications\TaskNotification("Admin deleted the task assigned to you: {$task->title}", $task->id));
                }
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }
        
        $task->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Task deleted successfully.');
    }

    // Show trashed tasks for admin
    public function recycle()
    {
        $tasks = Task::onlyTrashed()->latest()->get();
        return view('admin.tasks.recycle-bin', compact('tasks'));
    }

    // Restore trashed task
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();
        return redirect()->route('admin.tasks.recycle')->with('success', 'Task restored successfully.');
    }

    // Permanently delete trashed task
    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        
        // Notify the assigned user if task was assigned to someone (before permanent deletion)
        try {
            if ($task->user_id && $task->user_id != auth()->id()) {
                $assignedUser = \App\Models\User::find($task->user_id);
                if ($assignedUser) {
                    $assignedUser->notify(new \App\Notifications\TaskNotification("Admin permanently deleted the task assigned to you: {$task->title}", $task->id));
                }
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }
        
        $task->forceDelete();
        return redirect()->route('admin.tasks.recycle')->with('success', 'Task permanently deleted.');
    }

    // Bulk actions for admin recycle view
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $taskIds = $request->input('task_ids', []);

        if (empty($taskIds)) {
            return redirect()->route('admin.tasks.recycle')->with('error', 'No tasks selected.');
        }

        switch ($action) {
            case 'restore':
                Task::withTrashed()->whereIn('id', $taskIds)->restore();
                break;
            case 'delete':
                // Notify users before permanently deleting
                $tasks = Task::withTrashed()->whereIn('id', $taskIds)->get();
                foreach ($tasks as $task) {
                    try {
                        if ($task->user_id && $task->user_id != auth()->id()) {
                            $assignedUser = \App\Models\User::find($task->user_id);
                            if ($assignedUser) {
                                $assignedUser->notify(new \App\Notifications\TaskNotification("Admin permanently deleted the task assigned to you: {$task->title}", $task->id));
                            }
                        }
                    } catch (\Throwable $e) {
                        // ignore notification errors
                    }
                }
                Task::withTrashed()->whereIn('id', $taskIds)->forceDelete();
                break;
            default:
                return redirect()->route('admin.tasks.recycle')->with('error', 'Invalid action.');
        }

        return redirect()->route('admin.tasks.recycle')->with('success', 'Bulk action completed.');
    }
}
