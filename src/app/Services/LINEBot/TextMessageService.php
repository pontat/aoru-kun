<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class TextMessageService
{
    public function execute(LINEBot $bot, TextMessage $event, string $replyToken): void
    {
        $text = $event->getText();
        $textMessage = new TextMessageBuilder($text);
        $lineUser = LineUser::where('line_id', $event->getUserId())->firstOrFail();

        if ($text === '> 達成報告') {
            $textMessage = $this->achievementReport($lineUser);
        } elseif (preg_match('/> /', $text)) {
            $textMessage = $this->taskComplete($lineUser, $text);
        }

        $bot->replyMessage($replyToken, $textMessage);
    }

    private function achievementReport(LineUser $lineUser): TextMessageBuilder
    {
        $tasks = Task::where('line_user_id', $lineUser->id)
            ->whereDate('created_at', new Carbon())
            ->get();

        $quickReplyButtons = [];
        foreach ($tasks as $task) {
            $messageTemplate = new MessageTemplateActionBuilder($task->name, '> ' . $task->id);
            $quickReplyButton = new QuickReplyButtonBuilder($messageTemplate);
            $quickReplyButtons[] = $quickReplyButton;
        }
        $quickReplyMessage = new QuickReplyMessageBuilder($quickReplyButtons);

        return new TextMessageBuilder('どのタスクが終わった？', $quickReplyMessage);
    }

    private function taskComplete(LineUser $lineUser, string $text): TextMessageBuilder
    {
        $text = str_replace('> ', '', $text);
        $task = Task::where('line_user_id', $lineUser->id)->findOrFail($text);
        $task->fill(['is_completed' => true])->save();

        return new TextMessageBuilder($task->name . 'のタスクを完了にしたよ！お疲れ様！');
    }
}
