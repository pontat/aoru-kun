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
        $input = $request->only(['userId', 'displayName', 'pictureUrl']);

        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $input['userId'],
            'display_name' => $input['displayName'],
            'picture_url' => $input['pictureUrl'],
        ])->save();
        $lineUser->save();

        return $lineUser;
    }
}
