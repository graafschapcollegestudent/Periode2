<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "is_done" => "boolean",
        ]);

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    // GET /api/tasks/{task}
    public function show(Task $task)
    {
        return response()->json($task);
    }

    // PUT /api/tasks/{task}
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            "title" => "sometimes|required|string|max:255",
            "is_done" => "sometimes|boolean",
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    // DELETE /api/tasks/{task}
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }

    // PUT /api/task/{task}/complete
    public function complete(Task $task)
    {
        $task->update([
            'is_done' => true,
        ]);

        return response()->json($task);
    }

    // GET /api/task/complete
    public function completed()
    {
        $tasks = Task::where('is_done', true)->get();

        return response()->json($tasks);
    }
}
