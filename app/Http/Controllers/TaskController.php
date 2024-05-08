<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {

        // Create new task
        try {
            // Validation
            $validatedData = $request->validate([
                'description' => 'required|string|max:255',
                'due_date' => 'required|date',
                'todo_list_id' => 'required|exists:todo_lists,id',
            ]);

            $task = Task::create([
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'status' => 'pending', // Default status
                'todo_list_id' => $validatedData['todo_list_id'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error messages
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle database or other errors
            return response()->json(['error' => 'Failed to create task'], 500);
        }

        // Return response
        return response()->json($task, 201);
    }

    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $tasks = Task::orderBy('id', 'desc')->get();

        return response()->json($tasks);
    }

    public function getById($id)
    {
        $tasks = Task::findOrFail($id);

        return response()->json($tasks);
    }

    public function update(Request $request, $id)
    {

        try {
            // Validation
            $validatedData = $request->validate([
                'description' => 'required|string|max:255',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,completed',
            ]);

            // Find the task by ID
            $task = Task::findOrFail($id);

            // Update task attributes
            $task->description = $validatedData['description'];
            $task->due_date = $validatedData['due_date'];
            $task->status = $validatedData['status'];

            // Save the updated task
            $task->save();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error messages
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle database or other errors
            return response()->json(['error' => 'Failed to update task'], 500);
        }

        // Return response
        return response()->json($task);
    }


    public function destroy($id)
    {
        try {
            // Find the task by ID
            $task = Task::findOrFail($id);

            // Soft delete task
            $task->delete();
        } catch (\Exception $e) {
            // Handle database or other errors
            return response()->json(['error' => 'Failed to delete task'], 500);
        }

        // Return response
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
