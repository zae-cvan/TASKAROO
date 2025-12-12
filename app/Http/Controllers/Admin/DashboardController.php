<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::user();

        // Total users (exclude admin accounts)
        $totalUsers = User::where('role', '!=', 'admin')->count();

        // Tasks assigned by this admin — prefer explicit created_by column if present,
        // otherwise fall back to category marker set when admins create tasks.
        $adminTasksQuery = Task::query();
        // If the schema has `created_by`, include tasks where the admin is the creator
        // but also include older tasks that were marked with the legacy category.
        if (Schema::hasColumn('tasks', 'created_by')) {
            $adminTasksQuery->where(function($q) use ($admin) {
                $q->where('created_by', $admin->id)
                  ->orWhere('category', 'Task from Admin');
            });
        } else {
            $adminTasksQuery->where('category', 'Task from Admin');
        }

        $totalAssigned = $adminTasksQuery->count();
        $pendingAssigned = (clone $adminTasksQuery)->where('status', 'active')->count();
        $completedAssigned = (clone $adminTasksQuery)->where('status', 'completed')->count();

        // Recent tasks (for quick view on dashboard)
        if (Schema::hasColumn('tasks', 'created_by')) {
            $recentTasks = Task::where(function($q) use ($admin) {
                $q->where('created_by', $admin->id)
                  ->orWhere('category', 'Task from Admin');
            })->latest()->take(8)->get();
        } else {
            $recentTasks = Task::where('category', 'Task from Admin')->latest()->take(8)->get();
        }

        return view('admin.dashboard', compact('totalUsers', 'totalAssigned', 'pendingAssigned', 'completedAssigned', 'recentTasks'));
    }
}
