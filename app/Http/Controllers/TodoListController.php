<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = auth('api')->user();

        $todoLists = $user->todoLists()->orderBy('id', 'desc')->get();

        return response()->json($todoLists);
    }

    public function getById($id)
    {
        $todoList = TodoList::findOrFail($id);

        return response()->json($todoList);
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            /** @var User $user */
            $user = auth()->user();
            $todoList = $user->todoLists()->create([
                'name' => $request->name,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error messages
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create todo list'], 500);
        }

        return response()->json($todoList, 201);
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $todoList = TodoList::findOrFail($id);
            $todoList->name = $request->name;
            $todoList->save();

            return response()->json($todoList);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error messages
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update todo list'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the task by ID
            $todoList = TodoList::findOrFail($id);
            $todoList->delete();
        } catch (\Exception $e) {
            // Handle database or other errors
            return response()->json(['error' => 'Failed to delete todo list'], 500);
        }

        // Return response
        return response()->json(['message' => 'To-do list deleted successfully']);
    }
}
