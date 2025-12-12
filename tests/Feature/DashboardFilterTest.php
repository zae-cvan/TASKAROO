<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;

class DashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_tasks_from_admin_filter()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // admin created a task for the user
        $adminTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $admin->id]);
        // user created a task for themselves
        $personalTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id]);

        $this->actingAs($user)->get('/dashboard?filter=from_admin')
            ->assertStatus(200)
            ->assertSeeText($adminTask->title)
            ->assertDontSeeText($personalTask->title);
    }

    public function test_dashboard_shows_personal_filter()
    {
        $user = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create(['role' => 'user']);

        $personalTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $user->id]);
        $otherTask = Task::factory()->create(['user_id' => $user->id, 'created_by_id' => $other->id]);

        $this->actingAs($user)->get('/dashboard?filter=personal')
            ->assertStatus(200)
            ->assertSeeText($personalTask->title)
            ->assertDontSeeText($otherTask->title);
    }
}
