<?php

namespace App\Http\Controllers;

use App\Models\LineUser;
use App\Models\Task;

class LineUserController extends Controller
{
    public function findByLineId(string $lineId): ?LineUser
    {
        return LineUser::where('line_id', $lineId)->first();
    }
}
