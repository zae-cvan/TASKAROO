<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\OtpPasswordResetController;
use App\Http\Controllers\NotificationController;

// Landing page
Route::get('/', fn() => view('landing'))->name('landing');

// Local dev helper: quick login as seeded admin (only allowed from localhost or local env)
if (app()->environment('local') || request()->server('REMOTE_ADDR') === '127.0.0.1') {
    Route::get('/dev/login-as-admin', function () {
        $admin = App\Models\User::where('role', 'admin')->first();
        if (! $admin) {
            abort(404, 'No admin user');
        }
        \Illuminate\Support\Facades\Auth::loginUsingId($admin->id);
        request()->session()->regenerate();
        return redirect('/admin');
    });
}

// =======================
// GUEST ROUTES
// =======================
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [OtpPasswordResetController::class, 'showForgotForm'])->name('forgot.password');
    Route::post('/forgot-password', [OtpPasswordResetController::class, 'sendOtp'])->name('forgot.password.send');

    Route::get('/otp-verify', [OtpPasswordResetController::class, 'showOtpForm'])->name('otp.verify.form');
    Route::post('/otp-verify', [OtpPasswordResetController::class, 'verifyOtp'])->name('otp.verify');

    Route::get('/reset-password', [OtpPasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [OtpPasswordResetController::class, 'resetPassword'])->name('password.reset');
});

// =======================
// EMAIL VERIFICATION
// =======================
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// =======================
// AUTHENTICATED ROUTES
// =======================
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/productivity', [DashboardController::class, 'getProductivity'])->name('api.productivity');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/group-action', [TaskController::class, 'groupAction'])->name('tasks.groupAction');
    Route::post('/tasks/bulk-action', [TaskController::class, 'bulkAction'])->name('tasks.bulkAction');
    Route::get('/recycle-bin', [TaskController::class, 'recycle'])->name('tasks.recycle');
    Route::patch('/tasks/restore/{id}', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/tasks/force-delete/{id}', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');
    Route::patch('/tasks/{task}/pin', [TaskController::class, 'pin'])->name('tasks.pin');
    Route::patch('/tasks/{task}/unpin', [TaskController::class, 'unpin'])->name('tasks.unpin');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/uncomplete', [TaskController::class, 'uncomplete'])->name('tasks.uncomplete');
    Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
    Route::patch('/tasks/{task}/unarchive', [TaskController::class, 'unarchive'])->name('tasks.unarchive');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // ======================
    // Profile OTP Routes
    // ======================
    Route::post('/profile/send-password-otp', [ProfileController::class, 'sendPasswordOtp'])->name('password.sendOtp');
    Route::post('/profile/update-password-otp', [ProfileController::class, 'updatePasswordWithOtp'])->name('password.changeWithOtpDb');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.log');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);

    // Admin: Task assignment (simple role gating assumed)
    // Admin area (restricted by AdminMiddleware)
    Route::prefix('admin')->name('admin.')->middleware(App\Http\Middleware\AdminMiddleware::class)->group(function () {
        // Admin dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Task assignment
        Route::get('/tasks/assign', [App\Http\Controllers\Admin\TaskAssignmentController::class, 'index'])->name('tasks.assign');
        Route::post('/tasks/assign', [App\Http\Controllers\Admin\TaskAssignmentController::class, 'assign'])->name('tasks.assignPost');
        Route::post('/tasks/store-assign', [App\Http\Controllers\Admin\TaskAssignmentController::class, 'storeAndAssign'])->name('tasks.storeAndAssign');

        // Task management (show/edit/delete)
        // Admin tasks list (tasks created/assigned by this admin)
        Route::get('/tasks', [App\Http\Controllers\Admin\TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'show'])->name('tasks.show');
        Route::get('/tasks/{task}/edit', [App\Http\Controllers\Admin\TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [App\Http\Controllers\Admin\TaskController::class, 'destroy'])->name('tasks.destroy');

        // Admin Recycle Bin (view trashed tasks, restore, force delete, bulk)
        Route::get('/recycle-bin', [App\Http\Controllers\Admin\TaskController::class, 'recycle'])->name('tasks.recycle');
        Route::patch('/tasks/restore/{id}', [App\Http\Controllers\Admin\TaskController::class, 'restore'])->name('tasks.restore');
        Route::delete('/tasks/force-delete/{id}', [App\Http\Controllers\Admin\TaskController::class, 'forceDelete'])->name('tasks.forceDelete');
        Route::post('/tasks/bulk-action', [App\Http\Controllers\Admin\TaskController::class, 'bulkAction'])->name('tasks.bulkAction');

        // User management
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/toggle', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle');
    });

    // Speech-to-text (real-time recording page + AJAX endpoints)
    Route::get('/speech', [App\Http\Controllers\SpeechController::class, 'index'])->name('speech.index');
        Route::post('/speech/translate', [App\Http\Controllers\SpeechController::class, 'translate'])->name('speech.translate');
    Route::post('/speech/save', [App\Http\Controllers\SpeechController::class, 'save'])->name('speech.save');
        Route::delete('/speech/delete/{id}', [App\Http\Controllers\SpeechController::class, 'destroy'])->name('speech.delete');
});
