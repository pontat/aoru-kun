<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Response;

class FollowEventService
{
    public function execute(LINEBot $bot, FollowEvent $event, string $replyToken): Response
    {
        $lineId = $event->getUserId();
        $profile = $bot->getProfile($lineId);
        if (!$profile->isSucceeded()) {
            logger()->info('failed to get profile. skip processing.');
            return $bot->replyText($replyToken, '初めまして！！友達登録ありがとう！これからやる気を管理していくね！ファイト！');
        }
        $profile = $profile->getJSONDecodedBody();
        $lineUser = new LineUser();
        $lineUser->fill([
            'line_id' => $lineId,
            'display_name' => $profile['displayName'],
            'picture_url' => $profile['pictureUrl'],
        ])->save();

        return $bot->replyText(
            $replyToken,
            '初めまして！！友達登録ありがとう！これから' . $lineUser->display_name . 'さんのやる気を管理していくね！ファイト！'
        );
    }
}
