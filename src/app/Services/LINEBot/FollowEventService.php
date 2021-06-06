<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;

class FollowEventService
{
    public function execute(LINEBot $bot, FollowEvent $event): string
    {
        $lineId = $event->getUserId();
        $profile = $bot->getProfile($lineId);
        if (!$profile->isSucceeded()) {
            logger()->info('failed to get profile. skip processing.');
            return '友達登録おおきに！！';
        }
        $profile = $profile->getJSONDecodedBody();
        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $lineId,
            'display_name' => $profile['displayName'],
            'picture_url' => $profile['pictureUrl'],
        ])->save();

        return $lineUser->display_name . 'さん初めまして！！友達登録おおきに！！';
    }
}
