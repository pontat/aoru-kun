<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use LINE\LINEBot;
use LINE\LINEBot\Event\UnfollowEvent;

class UnFollowEventService
{
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function execute(UnfollowEvent $event): void
    {
        $lineId = $event->getUserId();
        LineUser::where('line_id', $lineId)->delete();
    }
}
