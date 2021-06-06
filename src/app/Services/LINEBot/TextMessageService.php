<?php

namespace App\Services\LINEBot;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class TextMessageService
{
    public function execute(LINEBot $bot, TextMessage $event): string
    {
        return $event->getText();
    }
}
