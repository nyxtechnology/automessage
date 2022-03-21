<?php

namespace Tests\Unit;

use App\Http\Controllers\SchedulingController;
use App\SchedulingMessage;
use Carbon\Carbon;
use Tests\Faker\MessageFaker;
use Tests\TestCase;

class SchedulingControllerTest extends TestCase
{
    /**
     * @group schedulingTest
     */
    public function testCalcDeliveryDate() {
        // arrange
        $schedulingController = new SchedulingController();
        $startDate = "12/31/2022";
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
        $dataInvalid = "12/32/2022";
        // act
        $dateCalc = $schedulingController->calcDeliveryDate($dataInvalid, $operator);
        // assert
        $this->assertEquals(null, $dateCalc);
    }

    /**
     * @group schedulingTest
     */
    public function testSaveMessage() {
        // arrange
        $schedulingController = new SchedulingController();
        $params = [
            'deliveryDate' => Carbon::now()->addDays(1)->toDate(),
            'waysDelivery' => [
                  [
                      'class' => "App\\Http\\Controllers\\TelegramController",
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
            'conditionsStopDelivery' => [
                'post.body.cardId' => 'wwutm7rsiJPX5ar3z',
                'post.header.x-forwarded-for' => '178.128.181.251',
                'post.body.description' => 'act-moveCard'
            ]
        ];

        // act
        $result = $schedulingController->saveMessage($params);

        // assert
        $this->assertDatabaseHas('scheduling_messages', [
            'id' => $result->id,
        ]);
    }

    /**
     * @group schedulingTest
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
                    'class' => "App\\Http\\Controllers\\TelegramController",
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

        // arrange
        $params['dateDelivery']['operation'] = "* days error";
        // act
        $result = $schedulingController->messageScheduling($params);
        // assert
        $this->assertEquals(null, $result);
    }


    /**
     * @group schedulingTest
     */
    public function testSaveSchedulingEmails()
    {
        $settings = [
            'externalId' => '1234FDE',
            'to' => 'teste@teste.com.br',
            'deliveryDate' => '2020-05-31 00:00:00',
            'subject' => 'Test',
            'eventStop'=> 'Test',
            'event' => 'Test'
        ];
        $schedulingController = new SchedulingController();
        $schedulingController->saveSchedulingEmails($settings);
        $this->assertDatabaseHas('email_schedulings', [
            'external_id' => '1234FDE',
        ]);
    }

    /**
     * @group schedulingTest
     */
    public function testDeleteSchedulingEmails()
    {
        $settings = [
            'params' => [
                'extenalId' => '1234FDE',
                'to' => 'teste@teste.com.br',
            ],
            'event' => 'Test'
        ];
        $schedulingController = new SchedulingController();
        $schedulingController->deleteSchedulingEmails($settings);
        $this->assertDatabaseMissing('email_schedulings', [
            'external_id' => '1234FDE'
        ]);
    }
}

