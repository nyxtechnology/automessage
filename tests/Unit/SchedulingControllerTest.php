<?php

namespace Tests\Unit;

use App\Http\Controllers\SchedulingController;
use App\SchedulingMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Tests\Faker\MessageFaker;
use Tests\TestCase;

class SchedulingControllerTest extends TestCase
{
    /**
     * @group schedulingControllerTest
     */
    public function testCalcDeliveryDate() {
        // arrange
        $schedulingController = new SchedulingController();
        $startDate = "2022/03/11";
        Carbon::setTestNow(Carbon::parse($startDate));
        $day = random_int(0, 100);
        $operator = "+ $day days";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->addDays($day)->toString(), $dateCalc->toString());
        // arrange
        $operator = "- $day days";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->subDays($day)->toString(), $dateCalc->toString());

        // arrange
        $operator = "+ $day week";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->addWeeks($day)->toString(), $dateCalc->toString());
        // arrange
        $operator = "- $day weeks";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->subWeeks($day)->toString(), $dateCalc->toString());

        // arrange
        $operator = "+ $day months";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->addMonths($day)->toString(), $dateCalc->toString());
        // arrange
        $operator = "- $day months";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->subMonths($day)->toString(), $dateCalc->toString());

        // arrange
        $operator = "+ $day year";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->addYears($day)->toString(), $dateCalc->toString());
        // arrange
        $operator = "- $day Years";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(Carbon::now()->subYears($day)->toString(), $dateCalc->toString());

        // arrange
        $operator = "$day error";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($startDate, $operator);
        // assert
        $this->assertEquals(null, $dateCalc);

        // arrange
        $operator = "- $day Years";
        $dataInvalid = "2022/12/32";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($dataInvalid, $operator);
        // assert
        $this->assertEquals(null, $dateCalc);
    }

    /**
     * @group schedulingControllerTest
     */
    public function testSaveMessage() {
        // arrange
        $schedulingController = new SchedulingController();
        $params = [
            'dateDelivery' => [
                'operation' => '+ 1 day',
                'startDate' => Carbon::today()->toDateString()
            ],
            'deliveryDate' => Carbon::now()->toDate(),
            'waysDelivery' => [
                  [
                      'controller' => "App\\Http\\Controllers\\TelegramController",
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
                      'controller' => "App\\Http\\Controllers\\TelegramController",
                      'methods' => [
                          [
                              'sendMessage' => [
                                  'to' => Config::get('telegram.test.chat_id'),
                                  'message' => "Text message3."
                              ]
                          ]
                        ],
                  ],
                ],
            'conditionsStopDelivery' => [
                [
                    'post.body.cardId' => 'wwutm7rsiJPX5ar3z',
                    'post.header.x-forwarded-for' => '178.128.181.251',
                    'post.body.description' => 'act-moveCard'
                ]
            ]
        ];

        // act
        $result = $schedulingController->saveMessage($params);

        // assert
        $this->assertDatabaseHas('scheduling_messages', [
            'id' => $result->id,
        ]);
        SchedulingMessage::destroy($result->id);
    }

    /**
     * @group schedulingControllerTest
     */
    public function testMessageScheduling() {
        // arrange
        $schedulingController = new SchedulingController();
        $params = [
            "dateDelivery" => [
                "startDate" => "12/31/2022",
                "operation" => "+ 3 days"
            ],
            'waysDelivery' => [
                [
                    'controller' => "App\\Http\\Controllers\\TelegramController",
                    'methods' => [
                        [
                            'sendMessage' => [
                                'to' => '123456789',
                                'message' => "Text message."
                            ]
                        ]
                    ]
                ]
            ],
        ];
        // act
        $result = $schedulingController->messageScheduling($params);
        // assert
        $this->assertDatabaseHas('scheduling_messages', [
            'id' => $result->id,
        ]);
        SchedulingMessage::destroy($result->id);

        // arrange
        $params['dateDelivery']['operation'] = "* days error";
        // act
        $result = $schedulingController->messageScheduling($params);
        // assert
        $this->assertEquals(null, $result);
    }

    /**
     * @group schedulingControllerTest
     */
    public function testStopOrRefreshScheduling() {
        // arrange
        $schedulingController = new SchedulingController();
        $days = random_int(1, 10);
        $params = [
            "dateDelivery" => [
                "startDate" => "2022/03/08",
                "operation" => "+ $days days"
            ],
            'waysDelivery' => [
                [
                    'controller' => "App\\Http\\Controllers\\TelegramController",
                    'methods' => [
                        [
                            'sendMessage' => [
                                'to' => '123456789',
                                'message' => "Text message."
                            ]
                        ]
                    ]
                ]
            ],
            "conditionsRefreshDelivery" => [
                [
                    "post.body.cardId" => "updateCard45678GG",
                    "post.header.host" => "https://mytest.com",
                    "post.body.description" => "act-moveCard"
                ]
            ]
        ];
        $messageScheduling = $schedulingController->messageScheduling($params);
        $post = '{"header": {"host": "https://mytest.com","messageType": "event", "x-forwarded-for": "178.128.181.251"},"body": {"to": "0123456789", "timezone": "São Paulo", "cardId": "updateCard45678GG", "user": "Test User", "description": "act-moveCard", "text": "It is a text", "event": "post.header.messageType","host": "post.header.host"}}';

        // act
        $result = $schedulingController->stopOrRefreshScheduling(json_decode($post));

        // assert
        $this->assertDatabaseHas('scheduling_messages', [
            'id' => $messageScheduling->id,
            'delivery_date' => Carbon::today()->addDays($days)->toDate(),
            'processed' => false
        ]);
        SchedulingMessage::destroy($messageScheduling->id);

        // arrange
        $params['conditionsStopDelivery'] = [
            [
                "post.body.cardId" => "updateCard45678GG",
                "post.header.host" => "https://mytest.com",
                "post.body.description" => "act-moveCard"
            ]
        ];
        $messageScheduling = $schedulingController->messageScheduling($params);

        // act
        $result = $schedulingController->stopOrRefreshScheduling(json_decode($post));

        // assert
        $this->assertDatabaseHas('scheduling_messages', [
            'id' => $messageScheduling->id,
            'processed' => true
        ]);
        SchedulingMessage::destroy($messageScheduling->id);
    }
}

