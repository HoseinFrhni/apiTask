<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_can_list_tasks()
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'description', 'status', 'user_id']
            ]);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'pending',
            'user_id' => $this->user->id
        ];

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tasks', $data)
            ->assertStatus(201)
            ->assertJson([
                'message' => 'وظیقه ایجاد شد'
            ]);
    }

    /** @test */
    public function it_can_show_a_task()
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$this->task->id}")
            ->assertStatus(200)
            ->assertJson(['id' => $this->task->id]);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $updatedData = ['title' => 'Updated Task Title'];

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tasks/{$this->task->id}", $updatedData)
            ->assertStatus(200)
            ->assertJson($updatedData);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tasks/{$this->task->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted('tasks', ['id' => $this->task->id]); // بررسی soft delete
    }

}
