<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // If current user is admin, redirect to admin dashboard (admins use admin area)
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = $user->id;

        // Auto-archive overdue tasks (active tasks only)
        Task::where('user_id', $userId)
            ->where('status', 'active')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->update(['status' => 'archived']);

        $status = $request->get('status', 'active');
        $query = Task::where('user_id', $userId);

        if ($status === 'recycle') {
            $tasks = Task::onlyTrashed()
                ->where('user_id', $userId)
                ->latest()
                ->get();
        } else {
            $query->where('status', $status);

            // CATEGORY FILTER (All / Task from Admin / My Task)
            if ($request->filled('filter') && $request->filter !== 'All') {
                if ($request->filter === 'Task from Admin') {
                    $query->where('category', 'Task from Admin');
                } elseif ($request->filter === 'My Task') {
                    $query->where('category', 'My Task');
                }
            }

            // URGENCY FILTER
            if ($request->filled('urgency') && in_array($request->urgency, ['very_urgent','urgent','normal','least_urgent'])) {
                $query->where('urgency', $request->urgency);
            }

            // SEARCH FILTER
            if ($request->filled('search')) {
                $q = $request->search;
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }

            // SORTING
            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'deadline_asc':
                        $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                              ->orderBy('due_date', 'asc');
                        break;
                    case 'deadline_desc':
                        $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                              ->orderBy('due_date', 'desc');
                        break;
                    case 'most_urgent':
                        // order by urgency priority from config (higher first)
                        $levels = config('urgency.levels');
                        // Build a CASE expression mapping
                        $cases = [];
                        $i = 4;
                        foreach (['very_urgent','urgent','normal','least_urgent'] as $k) {
                            $cases[] = "WHEN urgency = '{$k}' THEN {$i}";
                            $i--;
                        }
                        $caseSql = '(CASE '.implode(' ', $cases).' ELSE 0 END)';
                        $query->orderByRaw($caseSql.' DESC');
                        break;
                    case 'least_urgent':
                        $cases = [];
                        $i = 1;
                        foreach (['least_urgent','normal','urgent','very_urgent'] as $k) {
                            $cases[] = "WHEN urgency = '{$k}' THEN {$i}";
                            $i++;
                        }
                        $caseSql = '(CASE '.implode(' ', $cases).' ELSE 0 END)';
                        $query->orderByRaw($caseSql.' ASC');
                        break;
                    default:
                        $query->orderBy('is_pinned', 'desc')->latest();
                        break;
                }
            } else {
                $query->orderBy('is_pinned', 'desc')->latest();
            }

            $tasks = $query->get();
        }

        // --- Productivity Calculations ---
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

        // --- Deadline Today Feature ---
        $tasksDueToday = Task::where('user_id', $userId)
            ->whereDate('due_date', Carbon::today())
            ->where('status', 'active')
            ->get();
        $hasDeadlineToday = $tasksDueToday->isNotEmpty();

        return view('dashboard', compact(
            'tasks',
            'status',
            'todayProductivity',
            'weeklyProductivity',
            'monthlyProductivity',
            'tasksDueToday',
            'hasDeadlineToday'
        ));
    }

    public function getProductivity()
    {
        $userId = Auth::id();
        
        // --- Productivity Calculations ---
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

        return response()->json([
            'todayProductivity' => $todayProductivity,
            'weeklyProductivity' => $weeklyProductivity,
            'monthlyProductivity' => $monthlyProductivity
        ]);
    }
}
