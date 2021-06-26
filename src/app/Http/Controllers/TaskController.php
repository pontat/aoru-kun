<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

    public function history()
    {
        return Inertia::render(
            'TaskHistory',
            ['liffId' => env('LINE_LIFF_ID')]
        );
    }

    public function findAllByAuthUserAndTargetDate(string $targetDate): array
    {
        $tasks = Task::where('line_user_id', Auth::id())
            ->whereDate('created_at', new Carbon($targetDate))
            ->get();

        return $this->formatTask($tasks);
    }

    public function findAllByAuthUserAndTargetMonth(string $targetMonth)
    {
        $tasks = Task::where('line_user_id', Auth::id())
            ->whereYear('created_at', new Carbon($targetMonth))
            ->whereMonth('created_at', new Carbon($targetMonth))
            ->get();

        $formatTasks = $this->formatTask($tasks);

        $groupByTasks = collect($formatTasks)->groupBy('created_at');

        return $groupByTasks;
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

    public function complete(int $taskId): Task
    {
        $task = Task::findOrFail($taskId);
        $task->fill(['is_completed' => true]);
        $task->save();

        return $task;
    }

    private function formatTask(Collection $tasks): array
    {
        $formatTasks = [];
        foreach ($tasks as $task) {
            $formatTasks[] = [
                'id' => $task->id,
                'line_user_id' => $task->line_user_id,
                'name' => $task->name,
                'is_completed' => $task->is_completed,
                'created_at' => $task->created_at->format('Y-m-d'),
                'is_edit' => false,
            ];
        }

        return $formatTasks;
    }

    private function setTaskParams(Request $request): array
    {
        $params = $request->only('name');
        $params['line_user_id'] = Auth::id();

        return $params;
    }
}
