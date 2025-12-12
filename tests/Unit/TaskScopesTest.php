<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;

class TaskScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_most_and_least_priority_scopes()
    {
        $user = User::factory()->create();

        // create tasks with different priorities and due_dates
        Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id, 'priority' => 5, 'due_date' => now()->addDay()]);
        Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id, 'priority' => 1, 'due_date' => now()->addDays(10)]);
        Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id, 'priority' => 3, 'due_date' => now()->addDays(3)]);

        $most = Task::where('user_id', $user->id)->mostPriority()->first();
        $this->assertEquals(5, $most->priority);

        $least = Task::where('user_id', $user->id)->leastPriority()->first();
        $this->assertEquals(1, $least->priority);
    }

    public function test_from_admin_and_personal_scopes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Task created by admin for $user
        $adminTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $admin->id]);
        // Task created by user for themselves
        $personalTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id]);

        $fromAdmin = Task::where('user_id', $user->id)->fromAdmin()->get();
        $this->assertTrue($fromAdmin->contains('id', $adminTask->id));

        $personal = Task::where('user_id', $user->id)->personal($user->id)->get();
        $this->assertTrue($personal->contains('id', $personalTask->id));
    }
}
