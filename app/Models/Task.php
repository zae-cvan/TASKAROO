<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\TaskDeadlineNotification;
use App\Models\ActivityLog;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'color',
        'category',
        'urgency',
        'created_by',
        'is_pinned',
        'status', // use this instead of is_completed/is_archived
        'attachment',
        'checklist',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'checklist' => 'array',
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at', 'due_date'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Who created this task (could be admin or the user themselves)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Returns urgency meta (label, color, class)
    public function getUrgencyMetaAttribute()
    {
        $levels = config('urgency.levels', []);
        $key = $this->urgency ?? 'normal';
        return $levels[$key] ?? $levels['normal'];
    }

    // Accessors: convert UTC to Manila time
    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone('Asia/Manila') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone('Asia/Manila') : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone('Asia/Manila') : null;
    }

    // Accessor for checklist items (for backward compatibility)
    public function getChecklistItemsAttribute()
    {
        if (!$this->checklist || empty($this->checklist)) {
            return collect([]);
        }
        
        // Convert array to collection of objects
        return collect($this->checklist)->map(function($item) {
            return (object) [
                'title' => $item['title'] ?? '',
                'completed' => $item['completed'] ?? false
            ];
        });
    }

    // ===================== booted method =====================
    protected static function booted()
    {
        // Log activity
        static::created(function ($task) {
            ActivityLog::create([
                'user_id' => $task->user_id,
                'action' => "Created task: {$task->title}",
            ]);
        });

        static::updated(function ($task) {
            ActivityLog::create([
                'user_id' => $task->user_id,
                'action' => "Updated task: {$task->title}",
            ]);

            // Notify if due in <= 30 minutes
            if ($task->due_date) {
                $now = now();
                $diff = $task->due_date->diffInMinutes($now, false);
                if ($diff <= 30 && $diff >= 0) {
                    // Notify the assigned user
                    try {
                        $task->user->notify(new \App\Notifications\TaskDeadlineNotification($task, '30 minutes'));
                    } catch (\Throwable $e) {
                        // ignore
                    }

                    // Also notify the creator (e.g., admin who assigned the task)
                    if (!empty($task->created_by) && $task->created_by != $task->user_id) {
                        try {
                            $creator = \App\Models\User::find($task->created_by);
                            if ($creator) {
                                $creator->notify(new \App\Notifications\TaskNotification("Task '{$task->title}' assigned to {$task->user->name} is due in 30 minutes."));
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                }
            }
        });

        static::deleted(function ($task) {
            ActivityLog::create([
                'user_id' => $task->user_id,
                'action' => "Deleted task: {$task->title}",
            ]);
        });
    }

    // ===================== Helper methods for bulk actions =====================
    public static function markComplete($ids)
    {
        return static::whereIn('id', $ids)->update(['status' => 'completed']);
    }

    public static function markActive($ids)
    {
        return static::whereIn('id', $ids)->update(['status' => 'active']);
    }

    public static function markArchived($ids)
    {
        return static::whereIn('id', $ids)->update(['status' => 'archived']);
    }

    public static function moveToRecycle($ids)
    {
        return static::whereIn('id', $ids)->delete(); // soft delete
    }

    public static function restoreFromRecycle($ids)
    {
        return static::withTrashed()->whereIn('id', $ids)->restore();
    }
}
