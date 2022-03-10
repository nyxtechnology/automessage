<?php

namespace Tests\Unit;

use App\Http\Controllers\SchedulingController;
use Tests\Faker\MessageFaker;
use Tests\TestCase;

class SchedulingControllerTest extends TestCase
{
    public function testSaveSchedulingEmails()
    {
        $settings = [
            'params' => [
                'extenalId' => '1234FDE',
                'to' => 'teste@teste.com.br',
                'deliveryDate' => '2020-05-31 00:00:00',
                'subject' => 'Test',
                'eventStop'=> 'Test'
            ],
            'event' => 'Test'
        ];
        $schedulingController = new SchedulingController();
        $schedulingController->saveSchedulingEmails($settings);
        $this->assertDatabaseHas('email_schedulings', [
            'external_id' => '1234FDE',
        ]);
    }

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

