<?php

namespace App\Http\Controllers;

use App\Models\LineUser;
use Illuminate\Http\Request;

class LineUserController extends Controller
{
    public function findByLineId(string $lineId): ?LineUser
    {
        return LineUser::where('line_id', $lineId)->first();
    }

    public function create(Request $request): LineUser
    {
        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $request->input('userId'),
            'display_name' => $request->input('displayName'),
        ])->save();
        $lineUser->save();

        return $lineUser;
    }
}
