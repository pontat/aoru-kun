<?php

namespace App\Services\LINEBot;

use App\Models\LineUser;
use App\Models\Task;
use Carbon\Carbon;
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
        } else {
            $textMessage = $this->customMessage($text);
        }

        $bot->replyMessage($replyToken, $textMessage);
    }

    private function achievementReport(LineUser $lineUser): TextMessageBuilder
    {
        $tasks = Task::lineUserIncompleteTargetDate($lineUser, new Carbon())->get();

        $quickReplyButtons = [];
        foreach ($tasks as $index => $task) {
            if ($index > 12) break;
            $messageTemplate = mb_strlen($task->name) > 20
                ? new MessageTemplateActionBuilder(mb_substr($task->name, 0, 17, "UTF-8") . '...', '> ' . $task->id)
                : new MessageTemplateActionBuilder($task->name, '> ' . $task->id);

            $quickReplyButton = new QuickReplyButtonBuilder($messageTemplate);
            $quickReplyButtons[] = $quickReplyButton;
        }
        $quickReplyMessage = new QuickReplyMessageBuilder($quickReplyButtons);

        return $tasks->count() > 13
            ? new TextMessageBuilder('どのタスクが終わったかな？※先頭13個まで表示してるよ！', $quickReplyMessage)
            : new TextMessageBuilder('どのタスクが終わったかな？', $quickReplyMessage);
    }

    private function taskComplete(LineUser $lineUser, string $text): TextMessageBuilder
    {
        $text = str_replace('> ', '', $text);
        $task = Task::where('line_user_id', $lineUser->id)->findOrFail($text);
        $task->fill(['is_completed' => true])->save();

        $remainingTaskCount = Task::lineUserIncompleteTargetDate($lineUser, new Carbon())->count();

        return new TextMessageBuilder(
            $task->name . 'のタスクを完了にしたよ！お疲れ様！',
            $remainingTaskCount === 0
                ? '今日のタスクはこれで全部完了だよ！今日も一日お疲れ様でした！'
                : '今日のタスクは残り' . $remainingTaskCount . '個だよ！ファイト！'
        );
    }

    private function customMessage(string $text): TextMessageBuilder
    {
        $negativeNouns = [
            'やる気',
            '集中',
            '勉強',
        ];

        $negativeVerbs = [
            '無い',
            'ない',
            'キツイ',
            'キツい',
            'きつい',
            '辛い',
            'ツライ',
            'ツラい',
            'つらい',
        ];

        $hasNegativeNouns = false;
        foreach ($negativeNouns as $nouns) {
            if (strpos($text, $nouns) !== false) $hasNegativeNouns = true;
        }

        $hasNegativeVerb = false;
        foreach ($negativeVerbs as $verb) {
            if (strpos($text, $verb) !== false) $hasNegativeVerb = true;
        }

        if ($hasNegativeNouns && $hasNegativeVerb) {
            return new TextMessageBuilder('「もし今日が人生最後の日だったら、今日やることは本当にしたいことなのか？」この問いに「NO」が何日も続くのなら、なにかを変えなくてはならない。', '参照:Amazon  Steve Jobs (English Edition)  https://amzn.to/3cVuxve');
        }

        return new TextMessageBuilder('今日も一日頑張ろう！ファイト！');
    }
}
