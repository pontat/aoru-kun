<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render(
            'Task',
            ['liffId' => env('LINE_LIFF_ID')]
        );
    }

    public function findAllBylineUserIdAndTargetDate(Request $request, string $targetDate): array
    {
        $tasks = Task::where('line_user_id', Auth::id())
            ->whereDate('created_at', new Carbon($targetDate))
            ->get();

        $formatTasks = [];
        foreach ($tasks as $task) {
            $formatTasks[] = [
                'id' => $task->id,
                'line_user_id' => $task->line_user_id,
                'name' => $task->name,
                'is_edit' => false,
            ];
        }

        return $formatTasks;
    }

    public function create(Request $request): Task
    {
        $taskParams = $this->setTaskParams($request);

        $task = new Task();
        $task->fill($taskParams);
        $task->save();

        return $task;
    }

    public function update(Request $request, int $taskId): Task
    {
        $taskParams = $this->setTaskParams($request);

        $task = Task::findOrFail($taskId);
        $task->fill($taskParams);
        $task->save();

        return $task;
    }

    private function setTaskParams(Request $request): array
    {
        $params = $request->only('name');
        $params['line_user_id'] = Auth::id();

        return $params;
    }
}
