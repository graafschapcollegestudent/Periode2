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
        // maakt array met de data
        $data = [
            'title' => 'Test taak'
        ];
        // stuurt door naar de endpoint
        $response = $this->postJson('/api/tasks', $data);
        // checkt of de api een 201 teruggeeft en checkt of de response json de title bevat
        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title' => 'Test taak'
                 ]);
        // hier checkt ie gewoon of die is opgeslagen in de database
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test taak'
        ]);
    }

    public function test_can_update_task_description()
    { 
        // maakt task met title 'Oude omschrijving'
        $task = Task::factory()->create([
            'title' => 'Oude omschrijving'
        ]);

        //nieuwe title die ik wil opslaan
        $data = [
            'title' => 'Nieuwe omschrijving'
        ];

        //stuurt update request naar de api
        $response = $this->putJson("/api/task/{$task->id}", $data);

        // checkt of het OK is en nieuwe title krijgt
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title' => 'Nieuwe omschrijving'
                 ]);
        //checkt of dat de database is bijgewerkt met de nieuwe titel en dezelfde id
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Nieuwe omschrijving'
        ]);
    }

    public function test_can_mark_task_as_completed()
    {
        // maakt een task met is_done=false
        $task = Task::factory()->create([
            'is_done' => false
        ]);

        // stuurt een PUT naar de endpoint
        $response = $this->putJson("/api/task/{$task->id}/complete");

        // checkt of de response 200 is en dat is_done=true
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $task->id,
                     'is_done' => true
                 ]);

                 //checkt ook in de database of is_done=true
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_done' => true
        ]);
    }

    public function test_can_delete_a_task()
    {
        //maakt eerst een task in de database
        $task = Task::factory()->create();

        //stuurt een delete naar de endpoint
        $response = $this->deleteJson("/api/task/{$task->id}");

        //verwacht 204 No content
        $response->assertStatus(204);
        
        // controleert dat ie niet meer in de tasks-tabel staat
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_can_get_completed_tasks()
    {
        //maakt 2 tasks met is_done=true en 1 met is_done=false
        $completed = Task::factory()->count(2)->create([
            'is_done' => true
        ]);
        $incomplete = Task::factory()->create([
            'is_done' => false
        ]);

        //Get naar de endpoints
        $response = $this->getJson('/api/task/complete');

        //verwacht status 200 en precies 2 tasks in json
        $response->assertStatus(200)
                 ->assertJsonCount(2);

        // checkt of alle completed tasks in de reponse zit
        foreach ($completed as $task) {
            $response->assertJsonFragment([
                'id' => $task->id,
                'is_done' => true
            ]);
        }

        // checkts of de incomplete task niet in de response zit
        $response->assertJsonMissing([
            'id' => $incomplete->id
        ]);
    }
}
