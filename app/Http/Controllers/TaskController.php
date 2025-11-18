<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();

        // Agrupar tareas por estado
        $tasksByStatus = [
            'por_hacer' => $tasks->where('status', 'por_hacer'),
            'en_proceso' => $tasks->where('status', 'en_proceso'),
            'hecho' => $tasks->where('status', 'hecho'),
            'aprobado' => $tasks->where('status', 'aprobado'),
        ];

        return view('board', compact('tasksByStatus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'user' => ['nullable', 'string', 'max:100'],
        ]);

        $task = Task::create([
            'title' => $data['title'],
            'user' => $data['user'] ?? 'Anónimo',
            'status' => 'por_hacer',
        ]);

        event(new TaskUpdated($task, 'created'));

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'status' => ['required', 'in:por_hacer,en_proceso,hecho,aprobado'],
        ]);

        $task->update(['status' => $data['status']]);

        event(new TaskUpdated($task, 'updated'));

        return response()->json(['ok' => true, 'task' => $task]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        event(new TaskUpdated($task, 'deleted'));

        return response()->json(['ok' => true]);
    }
}
