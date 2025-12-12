<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Exclude admin accounts from the manage users listing
        $users = User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function toggleActive(Request $request, User $user)
    {
        $user->is_active = ! $user->is_active;
        $user->save();
        return back()->with('success', 'User status updated.');
    }

}
