<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LineUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LineAuthController extends Controller
{
    public function login(Request $request)
    {
        $accessToken = $request->bearerToken();

        $response = Http::get('https://api.line.me/oauth2/v2.1/verify', [
            'access_token' => $accessToken,
        ])->throw();

        if ($response->failed()) throw new Exception('Access token verification failed');

        $profileResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.line.me/v2/profile')->throw();

        $profileResponse = json_decode($profileResponse->body(), true);

        $lineUser = $this->findByLineId($profileResponse['userId']);
        if ($lineUser === null) {
            $lineUser = $this->create($profileResponse);
        }

        Auth::guard('web')->loginUsingId($lineUser->id);

        $request->session()->regenerate();

        return Auth::user();
    }

    private function findByLineId(string $lineId): ?LineUser
    {
        return LineUser::where('line_id', $lineId)->first();
    }

    private function create(array $profile): LineUser
    {
        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $profile['userId'],
            'display_name' => $profile['displayName'],
            'picture_url' => $profile['pictureUrl'],
        ]);
        $lineUser->save();

        return $lineUser;
    }
}
