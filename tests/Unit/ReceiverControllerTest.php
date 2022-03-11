<?php

namespace Tests\Unit;

use App\Http\Controllers\ReceiverController;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReceiverControllerTest extends TestCase
{
    public function testHandleWebhook() {
        // arrange
        $receiverController = new ReceiverController();
        $fakeRequest = new Request();
        $fakeRequest->headers->set('Content-Type', 'application/json');

        // act
        $result = $receiverController->handleWebhook($fakeRequest);

        // assert
        $this->assertEquals(200, $result->getStatusCode());
    }
}
