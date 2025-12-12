<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the user's activity logs.
     */
    public function index()
{
    // Fetch logs for the authenticated user, only for today
    $logs = ActivityLog::where('user_id', Auth::id())
        ->whereDate('created_at', Carbon::today()) // only today
        ->orderBy('created_at', 'desc')
        ->get(); // get all logs today, no pagination needed

    return view('activity-log', compact('logs'));
}
}
