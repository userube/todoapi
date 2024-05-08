<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
  use RefreshDatabase; // Refresh the database after each test

  /** @test */
  public function it_can_create_task()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $data = [
      'description' => 'Test task',
      'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
      'todo_list_id' => $todoList->id,
    ];

    $response = $this->postJson('/api/tasks/create', $data);

    //dd($response);

    $response->assertStatus(201)
      ->assertJsonFragment($data);
  }

  /** @test */
  public function it_can_list_tasks()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $tasks = Task::factory()->count(3)->create(['todo_list_id' => $todoList->id]);

    $response = $this->getJson('/api/tasks');
    $response->assertStatus(200)
      ->assertJsonCount(3);
  }

  /** @test */
  public function it_can_get_task_by_id()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $task = Task::factory()->create([
      'todo_list_id' => $todoList->id,
    ]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    $response = $this->getJson("/api/task/{$task->id}");

    $response->assertJsonFragment([
      'id' => $task->id,
      'description' => $task->description,
      'due_date' => $task->due_date->format('Y-m-d H:i:s'), // Format the date to match the expected format
      'status' => $task->status,
      'todo_list_id' => $task->todo_list_id,
      'created_at' => $task->created_at->toISOString(),
      'updated_at' => $task->updated_at->toISOString(),
      'deleted_at' => $task->deleted_at,
    ]);
  }

  /** @test */
  public function it_can_update_task()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $task = Task::factory()->create(['todo_list_id' => $todoList->id]);

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);

    $data = [
      'description' => 'Updated task',
      'due_date' => now()->addDay()->format('Y-m-d H:i:s'),  //now()->addDays(2),
      'status' => 'completed',
      'todo_list_id' => $todoList->id,
    ];

    $url =  "/api/task/" . $task->id;
    $response = $this->putJson($url, $data);

    $response->assertStatus(200)
      ->assertJsonFragment($data);
  }

  /** @test */
  public function it_can_soft_delete_task()
  {

    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $task = Task::factory()->create(['todo_list_id' => $todoList->id]);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    $response = $this->deleteJson('api/task/' . $task->id);

    $response->assertStatus(200)
      ->assertJson(['message' => 'Task deleted successfully']);

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
  }
}
