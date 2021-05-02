<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;

class FollowEventService
{
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function execute(FollowEvent $event): string
    {
        $lineId = $event->getUserId();
        $profile = $this->bot->getProfile($lineId);
        if (!$profile->isSucceeded()) {
            logger()->info('failed to get profile. skip processing.');
            return '友達登録に失敗してもうた！すまんが、一度ブロックした後にブロック解除してもらえへんか？';
        }
        $profile = $profile->getJSONDecodedBody();
        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $lineId,
            'display_name' => $profile['displayName'],
        ])->save();

        return $lineUser->display_name . 'さん初めまして！！';
    }
}
