<?php

namespace Tests\Unit;

use App\Events\WebhookReceived;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Listeners\CalledWebhook;

class CalledWebhookTest extends TestCase
{
    /**
     * @group webhookTest
     */
    public function testHandle()
    {
        // arrange
        $listener = new CalledWebhook();
        // TODO: change to email test
        $classes = [
            [
                'class' => "App\\Http\\Controllers\\TelegramController",
                'methods' => [
                    [
                        'sendMessage' => [
                            'to' => Config::get('telegram.test.chat_id'),
                            'message' => "Text message1."
                        ]
                    ],
                    [
                        'sendMessage' => [
                            'to' => Config::get('telegram.test.chat_id'),
                            'message' => "Text message2."
                        ]
                    ]
                ],
            ],
            [
                'class' => "App\\Http\\Controllers\\TelegramController",
                'methods' => [
                    [
                        'sendMessage' => [
                            'to' => Config::get('telegram.test.chat_id'),
                            'message' => "Text message3."
                        ]
                    ]
                ],
            ],
        ];
        $event = new WebhookReceived($classes);

        // act
        $listener->handle($event);

        // TODO: fix fake assert when we have a real email test
        // assert
        $this->assertTrue(true);
    }
}
