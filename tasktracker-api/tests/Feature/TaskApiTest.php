<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_tasks()
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_a_task()
    {
        $data = [
            'title' => 'Test taak'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title' => 'Test taak'
                 ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test taak'
        ]);
    }

    public function test_can_update_task_description()
    {
        $task = Task::factory()->create([
            'title' => 'Oude omschrijving'
        ]);

        $data = [
            'title' => 'Nieuwe omschrijving'
        ];

        $response = $this->putJson("/api/task/{$task->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title' => 'Nieuwe omschrijving'
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Nieuwe omschrijving'
        ]);
    }

    public function test_can_mark_task_as_completed()
    {
        $task = Task::factory()->create([
            'is_done' => false
        ]);

        $response = $this->putJson("/api/task/{$task->id}/complete");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $task->id,
                     'is_done' => true
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_done' => true
        ]);
    }

    public function test_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/task/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_can_get_completed_tasks()
    {
        $completed = Task::factory()->count(2)->create([
            'is_done' => true
        ]);
        $incomplete = Task::factory()->create([
            'is_done' => false
        ]);

        $response = $this->getJson('/api/task/complete');

        $response->assertStatus(200)
                 ->assertJsonCount(2);

        foreach ($completed as $task) {
            $response->assertJsonFragment([
                'id' => $task->id,
                'is_done' => true
            ]);
        }

        $response->assertJsonMissing([
            'id' => $incomplete->id
        ]);
    }
}
