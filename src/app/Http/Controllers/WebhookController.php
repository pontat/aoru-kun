<?php

namespace App\Http\Controllers;

use App\Services\LINEBot\FollowEventService;
use App\Services\LINEBot\TextMessageService;
use App\Services\LINEBot\UnFollowEventService;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\SignatureValidator;

class WebhookController extends Controller
{
    private $followEventService;
    private $textMessageService;
    private $unFollowEventService;

    public function __construct(
        FollowEventService $followEventService,
        TextMessageService $textMessageService,
        UnFollowEventService $unFollowEventService
    ) {
        $this->followEventService = $followEventService;
        $this->textMessageService = $textMessageService;
        $this->unFollowEventService = $unFollowEventService;
    }

    /**
     * Webhook function
     *
     * @param Request $request
     * @return void
     */
    public function __invoke(Request $request): void
    {
        $httpClient = new CurlHTTPClient(env('LINE_ACCESS_TOKEN'));
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_CHANNEL_SECRET')]);

        if (!array_key_exists('HTTP_' . HTTPHeader::LINE_SIGNATURE, $_SERVER)) {
            abort(400);
        };
        $signature = $_SERVER['HTTP_' . HTTPHeader::LINE_SIGNATURE];
        if (!SignatureValidator::validateSignature($request->getContent(), env('LINE_CHANNEL_SECRET'), $signature)) {
            abort(400);
        }

        $events = $bot->parseEventRequest($request->getContent(), $signature);
        foreach ($events as $event) {
            $replyToken = $event->getReplyToken();

            switch (true) {
                case $event instanceof LINEBot\Event\FollowEvent:
                    $this->followEventService->execute($bot, $event, $replyToken);
                    break;
                case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
                    $this->textMessageService->execute($bot, $event, $replyToken);
                    break;
                case $event instanceof LINEBot\Event\UnfollowEvent:
                    $this->unFollowEventService->execute($bot, $event);
                    break;
                default:
                    $body = $event->getEvent();
                    logger()->warning('Unknown event. [' . get_class($event) . ']', compact('body'));

                    $replyMessage = 'Not supported. [' . get_class($event) . '][' . $event->getType() . ']';
                    $bot->replyText($replyToken, $replyMessage);
            }
        }
    }
}
