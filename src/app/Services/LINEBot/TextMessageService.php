<?php

namespace App\Services\LINEBot;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class TextMessageService
{
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    public function execute(TextMessage $event): string
    {
        return $event->getText();
    }
}
