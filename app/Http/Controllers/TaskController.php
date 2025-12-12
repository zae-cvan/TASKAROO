<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Notifications\TaskNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Auto-pin endpoint for AJAX
    public function pin(Request $request, Task $task)
    {
        $this->authorize('update', $task); // optional: ensure user owns task
        $task->is_pinned = true;
        $task->save();
        return response()->json(['success' => true]);
    }
    public function complete(Task $task)
    {
        $task->update([
            'status' => 'completed'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Completed task',
            'details' => 'Task ID: ' . $task->id
        ]);

        // Notify the creator (if present and not the same user)
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new TaskNotification("Task '{$task->title}' was completed by {$task->user->name}"));
                }
            }
        } catch (\Throwable $e) {
            // swallow notification errors
        }

        return redirect()->route('dashboard')->with('success', 'Task marked as completed!');
    }

    public function index()
    {
        // Determine target user id: default to current user, but allow admins to view another user's tasks via ?assigned_to=
        $targetUserId = Auth::id();
        $restrictToCreator = false; // when true, only show tasks created/assigned by the current admin
        if (request()->has('assigned_to') && Auth::user() && Auth::user()->role === 'admin') {
            $requested = (int) request('assigned_to');
            // basic safety: ensure the user exists
            if (User::where('id', $requested)->exists()) {
                $targetUserId = $requested;
                // When an admin is viewing another user's tasks, only show tasks the admin created/assigned
                if ($targetUserId !== Auth::id()) {
                    $restrictToCreator = true;
                }
            }
        }

        // 1️⃣ Auto-archive overdue tasks (active only) for the target user
        $autoArchiveQuery = Task::where('user_id', $targetUserId)
            ->where('status', 'active')
            ->where('due_date', '<', now('UTC'));
        if ($restrictToCreator && Schema::hasColumn('tasks', 'created_by')) {
            $autoArchiveQuery->where('created_by', Auth::id());
        }
        $autoArchiveQuery->update(['status' => 'archived']);

        // Determine which status view the user requested (active/completed/archived)
        $status = request('status', 'active');

        if ($status === 'recycle') {
            // recycle handled by separate route, but keep safe fallback
            $trashQuery = Task::onlyTrashed()->where('user_id', $targetUserId);
            if ($restrictToCreator && Schema::hasColumn('tasks', 'created_by')) {
                $trashQuery->where('created_by', Auth::id());
            }
            $tasks = $trashQuery->latest()->get();
        } else {
            $query = Task::where('user_id', $targetUserId);
            if ($restrictToCreator && Schema::hasColumn('tasks', 'created_by')) {
                $query->where('created_by', Auth::id());
            }
            if ($status) {
                $query->where('status', $status);
            }
            $tasks = $query->orderBy('is_pinned', 'desc')->latest()->get();
        }

        // Convert due_date to Carbon for safe comparison
        $tasks->transform(function($task) {
            $task->due_date = $task->due_date ? Carbon::parse($task->due_date) : null;
            return $task;
        });

        // 3️⃣ Auto-pin tasks based on deadline urgency (Hybrid approach)
        $nearDeadlineTasks = $tasks->where('is_pinned', false) // exclude manual pins
            ->filter(function($task) {
                if (!$task->due_date) return false;
                $now = now('Asia/Manila');
                $due = $task->due_date->copy()->timezone('Asia/Manila');

                $threeDaysBefore = $due->copy()->subDays(3);
                $oneDayBefore = $due->copy()->subDay();
                $thirtyMinsBefore = $due->copy()->subMinutes(30);

                // Check if now is within 30 mins, 1 day, or 3 days before due
                return $now->between($thirtyMinsBefore, $due) 
                    || $now->between($oneDayBefore, $due) 
                    || $now->between($threeDaysBefore, $due);
            })
            ->sortBy(function($task) {
                $now = now('Asia/Manila');
                return $task->due_date->copy()->timezone('Asia/Manila')->diffInMinutes($now);
            })
            ->take(3); // Top 3 urgent tasks

        $nearDeadlineIds = $nearDeadlineTasks->pluck('id');

        // 4️⃣ Mark auto_pinned flag and assign urgency for badges
        $tasks->transform(function($task) use ($nearDeadlineIds) {
            $task->auto_pinned = $nearDeadlineIds->contains($task->id);

            if ($task->auto_pinned && $task->due_date) {
                $minutesLeft = $task->due_date->diffInMinutes(now('Asia/Manila'));
                if ($minutesLeft <= 30) $task->urgency = 'red';
                elseif ($minutesLeft <= 1440) $task->urgency = 'orange'; // 1 day
                else $task->urgency = 'yellow'; // 3 days
            }

            return $task;
        });

        // 5️⃣ Calculate productivity
        $todayProductivity = $this->calculateTodayProductivity($tasks);
        $weeklyProductivity = $this->calculateWeeklyProductivity($tasks);
        $monthlyProductivity = $this->calculateMonthlyProductivity($tasks);

        // --- Deadline Today Feature for dashboard view ---
        $dueTodayQuery = Task::where('user_id', $targetUserId)
            ->whereDate('due_date', Carbon::today())
            ->where('status', 'active');
        if ($restrictToCreator && Schema::hasColumn('tasks', 'created_by')) {
            $dueTodayQuery->where('created_by', Auth::id());
        }
        $tasksDueToday = $dueTodayQuery->get();
        $hasDeadlineToday = $tasksDueToday->isNotEmpty();

        // Prepare viewing user info (when admin views another user's tasks)
        $viewingUser = null;
        if ($targetUserId !== Auth::id()) {
            $viewingUser = User::find($targetUserId);
        }

        // 6️⃣ Return view (include status so tabs can highlight correctly)
        return view('dashboard', compact(
            'tasks',
            'todayProductivity',
            'weeklyProductivity',
            'monthlyProductivity',
            'tasksDueToday',
            'hasDeadlineToday',
            'status',
            'viewingUser'
        ));

        
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'urgency' => 'required|in:very_urgent,urgent,normal,least_urgent',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt|max:5120',
        ]);

        $dueDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->due_date, 'Asia/Manila');
        if ($dueDate->lt(now('Asia/Manila'))) {
            return back()->withInput()->withErrors(['due_date' => 'Due date and time must not be in the past.']);
        }

        $task = new Task();
        $task->user_id = Auth::id();
        // Only set created_by if the column exists (migration may not have been run yet)
        if (Schema::hasColumn('tasks', 'created_by')) {
            $task->created_by = Auth::id();
        }
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $dueDate->setTimezone('UTC');
        $task->category = $request->category ?? 'My Task';
        $task->urgency = $request->urgency;
        // If color not explicitly provided, use urgency mapping name (matches color radio values)
        $urgencyLevels = config('urgency.levels');
        $task->color = $request->color ?? ($urgencyLevels[$task->urgency]['name'] ?? 'violet');
        $task->is_pinned = $request->has('is_pinned');
        $task->status = 'active';

        // Handle file attachment upload
        if ($request->hasFile('attachment')) {
            try {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;
            } catch (\Throwable $e) {
                // ignore upload errors and continue
            }
        }
        $task->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created a task',
            'description' => 'Created task: ' . $task->title,
        ]);

        event(new TaskCreated($task));

        $task->user->notify(
            new \App\Notifications\TaskDeadlineNotification($task, 'Now')
        );

        return redirect()->route('dashboard')->with('success', 'Task created!');
    }

    public function show($id)
    {
        // Allow viewing trashed tasks
        $task = Task::withTrashed()->findOrFail($id);
        $this->authorizeTask($task);
        return view('tasks.show', compact('task'));
    }

    public function edit($id)
    {
        // Allow editing trashed tasks
        $task = Task::withTrashed()->findOrFail($id);
        try {
            $this->authorizeTask($task);
        } catch (\Throwable $e) {
            // If authorization fails, redirect with a helpful message instead of a blank/403 page
            return redirect()->route('dashboard')->with('error', 'You are not authorized to edit this task.');
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, $id)
    {
        // Allow updating trashed tasks
        $task = Task::withTrashed()->findOrFail($id);
        $this->authorizeTask($task);

        // Preserve the current status - don't change it unless explicitly needed
        $originalStatus = $task->status;
        $statusChanged = false;

        if ($request->due_date) {
            $task->due_date = Carbon::parse($request->due_date, 'Asia/Manila')->setTimezone('UTC');
            // Only auto-unarchive if task was archived and new due date is in future
            if ($originalStatus === 'archived' && $task->due_date >= now('UTC')) {
                $task->status = 'active';
                $statusChanged = true;
            }
        }

        $task->title = $request->title ?? $task->title;
        $task->description = $request->description ?? $task->description;
        $task->category = $request->category ?? $task->category;
        if ($request->filled('urgency')) {
            $task->urgency = $request->urgency;
            $urgencyLevels = config('urgency.levels');
            $task->color = $request->color ?? ($urgencyLevels[$task->urgency]['name'] ?? $task->color);
        } else {
            $task->color = $request->color ?? $task->color;
        }
        $task->is_pinned = $request->has('is_pinned');

        // Explicitly preserve status if it wasn't changed by due_date logic above
        if (!$statusChanged) {
            $task->status = $originalStatus;
        }

        // Handle checklist items - store as JSON
        if ($request->has('checklist')) {
            $checklistItems = [];
            $checklistData = $request->input('checklist', []);
            $checklistCompleted = $request->input('checklist_completed', []);
            
            // Process checklist items from form
            foreach ($checklistData as $index => $title) {
                $title = trim($title);
                if (!empty($title)) {
                    // Get completion status from form
                    $completed = isset($checklistCompleted[$index]) && $checklistCompleted[$index] == '1';
                    
                    $checklistItems[] = [
                        'title' => $title,
                        'completed' => $completed
                    ];
                }
            }
            
            // Store checklist as JSON
            $task->checklist = $checklistItems;
        }

        // Handle attachment replacement
        if ($request->hasFile('attachment')) {
            try {
                $newPath = $request->file('attachment')->store('attachments', 'public');
                // delete old
                if ($task->attachment && Storage::disk('public')->exists($task->attachment)) {
                    Storage::disk('public')->delete($task->attachment);
                }
                $task->attachment = $newPath;
            } catch (\Throwable $e) {}
        }

        $task->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated a task',
            'description' => 'Updated task: ' . $task->title,
        ]);

        // Notify creator (admin) that the assigned user updated the task
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = \App\Models\User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new \App\Notifications\TaskNotification("{$task->user->name} updated task: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }

        // Redirect back to show page if coming from there, or recycle bin if task is trashed
        if ($request->get('back') === 'show') {
            return redirect()->route('tasks.show', $task->id)->with('success', 'Task updated!');
        } elseif ($task->trashed()) {
            return redirect()->route('tasks.recycle')->with('success', 'Task updated!');
        }
        
        return redirect()->route('dashboard')->with('success', 'Task updated!');
    }

    public function archive(Task $task)
    {
        $this->authorizeTask($task);
        $task->update(['status' => 'archived']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Archived a task',
            'description' => 'Archived task: ' . $task->title,
        ]);

        // Notify creator (admin) that the assigned user archived the task
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = \App\Models\User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new \App\Notifications\TaskNotification("{$task->user->name} archived task: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {}

        return back()->with('success', 'Task archived!');
    }

    // notify creator when user archives
    public function archiveNotify(Task $task)
    {
        // Not used directly; notifications handled in archive method above when appropriate.
    }

    public function unarchive(Task $task)
    {
        $this->authorizeTask($task);
        $task->update(['status' => 'active']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Unarchived a task',
            'description' => 'Unarchived task: ' . $task->title,
        ]);

        // Notify creator (admin) that the assigned user unarchived the task
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = \App\Models\User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new \App\Notifications\TaskNotification("{$task->user->name} unarchived task: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {}

        return back()->with('success', 'Task unarchived!');
    }

    // Notify creator when user unarchives
    // (Reuse notification call similar to update)
    // We'll add notification here as well
    public function unarchiveNotify(Task $task)
    {
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = \App\Models\User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new \App\Notifications\TaskNotification("{$task->user->name} unarchived task: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {}
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $title = $task->title;
        $task->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Moved task to recycle bin',
            'description' => 'Deleted task: ' . $title,
        ]);

        // Notify creator (admin) that the assigned user moved it to recycle
        try {
            $this->notifyCreatorOnDelete($task);
        } catch (\Throwable $e) {}

        return redirect()->route('tasks.recycle')->with('success', 'Task moved to Recycle Bin.');
    }

    // Notify creator when user deletes (moves to recycle)
    private function notifyCreatorOnDelete(Task $task)
    {
        try {
            if (Schema::hasColumn('tasks', 'created_by') && $task->created_by && $task->created_by != Auth::id()) {
                $creator = \App\Models\User::find($task->created_by);
                if ($creator) {
                    $creator->notify(new \App\Notifications\TaskNotification("{$task->user->name} moved task to Recycle Bin: {$task->title}"));
                }
            }
        } catch (\Throwable $e) {}
    }


    public function unpin(Task $task)
    {
        $this->authorizeTask($task);
        $task->update(['is_pinned' => false]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Unpinned a task',
            'description' => 'Unpinned task: ' . $task->title,
        ]);

        return back()->with('success', 'Task unpinned!');
    }

    public function recycle()
    {
        $tasks = Task::onlyTrashed()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $status = 'recycle';

        // Provide deadline info so dashboard view can render safely
        $tasksDueToday = Task::where('user_id', Auth::id())
            ->whereDate('due_date', Carbon::today())
            ->where('status', 'active')
            ->get();
        $hasDeadlineToday = $tasksDueToday->isNotEmpty();

        // --- Productivity Calculations ---
        $userId = Auth::id();
        $todayStart = Carbon::today();

        // Daily productivity (always calculated)
        $allTasksToday = Task::where('user_id', $userId)
            ->where('created_at', '>=', $todayStart)
            ->get();
        $completedToday = $allTasksToday->where('status', 'completed')->count();
        $todayProductivity = $allTasksToday->count() > 0
            ? round(($completedToday / $allTasksToday->count()) * 100, 1)
            : 0;

        // Weekly productivity (only if at least 1 week has passed)
        $weekStart = Carbon::now()->subWeek();
        $allTasksThisWeek = Task::where('user_id', $userId)
            ->where('created_at', '>=', $weekStart)
            ->get();

        if ($allTasksThisWeek->count() >= 1 && Carbon::now()->diffInDays($allTasksThisWeek->min('created_at')) >= 7) {
            $completedThisWeek = $allTasksThisWeek->where('status', 'completed')->count();
            $weeklyProductivity = round(($completedThisWeek / $allTasksThisWeek->count()) * 100, 1);
        } else {
            $weeklyProductivity = 0; // Not yet a full week
        }

        // Monthly productivity (only if at least 1 month has passed)
        $monthStart = Carbon::now()->subMonth();
        $allTasksThisMonth = Task::where('user_id', $userId)
            ->where('created_at', '>=', $monthStart)
            ->get();

        if ($allTasksThisMonth->count() >= 1 && Carbon::now()->diffInDays($allTasksThisMonth->min('created_at')) >= 30) {
            $completedThisMonth = $allTasksThisMonth->where('status', 'completed')->count();
            $monthlyProductivity = round(($completedThisMonth / $allTasksThisMonth->count()) * 100, 1);
        } else {
            $monthlyProductivity = 0; // Not yet a full month
        }

        return view('dashboard', compact(
            'tasks',
            'status',
            'tasksDueToday',
            'hasDeadlineToday',
            'todayProductivity',
            'weeklyProductivity',
            'monthlyProductivity'
        ));
    }

    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $this->authorizeTask($task);
        $task->restore();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Restored a task',
            'description' => 'Restored task: ' . $task->title,
        ]);

        return back()->with('success', 'Task restored!');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $title = $task->title;
        $task->forceDelete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Permanently deleted a task',
            'description' => 'Deleted permanently: ' . $title,
        ]);

        return redirect()->route('tasks.recycle')->with('success', 'Task permanently deleted!');
    }

    private function authorizeTask(Task $task)
    {
        // Allow if current user is the assigned owner
        if ($task->user_id === Auth::id()) {
            return;
        }

        // Allow if the tasks table has a created_by column and the current user created the task
        if (Schema::hasColumn('tasks', 'created_by') && $task->created_by == Auth::id()) {
            return;
        }

        // Otherwise forbidden
        abort(403);
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $taskIds = $request->input('task_ids', []);
        $redirect = $request->input('redirect_to', route('dashboard'));

        if (empty($taskIds)) {
            return redirect()->to($redirect)->with('error', 'No tasks selected.');
        }

        switch ($action) {
            case 'complete':
                Task::whereIn('id', $taskIds)->update(['status' => 'completed']);
                break;
            case 'uncomplete':
                Task::whereIn('id', $taskIds)->update(['status' => 'active']);
                break;
            case 'archive':
                Task::whereIn('id', $taskIds)->update(['status' => 'archived']);
                break;
            case 'recycle':
                Task::whereIn('id', $taskIds)->delete();
                break;
            case 'restore':
                Task::withTrashed()->whereIn('id', $taskIds)->restore();
                break;
            case 'delete':
                Task::withTrashed()->whereIn('id', $taskIds)->forceDelete();
                break;
            default:
                return redirect($redirect)->with('error', 'Invalid action.');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Bulk action: ' . $action,
            'description' => 'Affected tasks: ' . implode(', ', $taskIds),
        ]);

        return redirect($redirect)->with('success', 'Bulk action completed.');
    }

    // ✅ Productivity helpers
    private function calculateTodayProductivity($tasks)
    {
        return $tasks->filter(fn($task) => $task->status === 'completed' && $task->due_date && $task->due_date->isToday())->count();
    }

    private function calculateWeeklyProductivity($tasks)
    {
        return $tasks->filter(fn($task) => $task->status === 'completed' && $task->due_date && $task->due_date->isCurrentWeek())->count();
    }

    private function calculateMonthlyProductivity($tasks)
    {
        return $tasks->filter(fn($task) => $task->status === 'completed' && $task->due_date && $task->due_date->isCurrentMonth())->count();
    }
}
