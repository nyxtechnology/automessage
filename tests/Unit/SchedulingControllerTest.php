<?php

namespace Tests\Unit;

use App\Http\Controllers\SchedulingController;
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
        $this->assertEquals(Carbon::now()->toString(), $dateCalc->toString());
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

