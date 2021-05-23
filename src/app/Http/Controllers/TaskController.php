<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        $formatTasks = [];
        foreach ($tasks as $task) {
            $formatTasks[] = [
                'id' => $task->id,
                'line_user_id' => $task->line_user_id,
                'name' => $task->name,
                'is_edit' => false,
            ];
        }

        return Inertia::render(
            'Task',
            ['tasks' => $formatTasks]
        );
    }

    public function store(Request $request)
    {
        $taskParams = $this->setTaskParams($request);

        $task = new Task($taskParams);
        $task->save();

        return $task;
    }

    public function update(Request $request, int $taskId)
    {
        $taskParams = $this->setTaskParams($request);

        $task = Task::findOrFail($taskId);
        $task->fill($taskParams);
        $task->save();

        return $task;
    }

    private function setTaskParams(Request $request): array
    {
        return $request->only(['line_user_id', 'name']);
    }
}
