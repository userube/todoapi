<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoListControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_list_todo_lists()
  {
    $user = User::factory()->create();
    $todoLists = TodoList::factory()->count(1)->create(['user_id' => $user->id]);
    $this->actingAs($user, 'api');
    $response = $this->getJson('/api/todos');
    $response->assertStatus(200);

    $expectedJson = $todoLists->map(function ($todoList) {
      return [
        'id' => $todoList->id,
        'name' => $todoList->name,
        'user_id' => $todoList->user_id,
        'created_at' => $todoList->created_at->toISOString(),
        'updated_at' => $todoList->updated_at->toISOString(),
      ];
    })->toArray();

    $response->assertJson($expectedJson);
  }

  /** @test */
  public function it_can_get_todo_list_by_id()
  {
    $user = User::factory()->create();
    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'api');
    $response = $this->getJson("/api/todo/{$todoList->id}");
    $response->assertStatus(200);
    $response->assertJson($todoList->toArray());
  }

  /** @test */
  public function it_can_create_todo_list()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $data = ['name' => 'Test Todo List'];
    $response = $this->postJson('/api/todo-lists/create', $data);
    $response->assertStatus(201);
    $response->assertJsonFragment($data);
  }

  /** @test */
  public function it_can_update_todo_list()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);
    $newName = 'Updated Todo List Name';
    $response = $this->putJson("/api/todo/{$todoList->id}", ['name' => $newName]);
    $response->assertStatus(200);
    $this->assertEquals($newName, $todoList->fresh()->name);
  }

  /** @test */
  public function it_can_soft_delete_todo_list()
  {
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    $todoList = TodoList::factory()->create([
      'user_id' => $user->id,
    ]);

    $response = $this->deleteJson("/api/todo/{$todoList->id}");
    $response->assertStatus(200);
    $this->assertNotNull($todoList->fresh()->deleted_at);
  }
}
