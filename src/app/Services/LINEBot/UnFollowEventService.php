<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use LINE\LINEBot;
use LINE\LINEBot\Event\UnfollowEvent;

class UnFollowEventService
{
    public function execute(LINEBot $bot, UnfollowEvent $event): void
    {
        $lineId = $event->getUserId();
        LineUser::where('line_id', $lineId)->delete();
    }
}
