<?php

namespace Tests\Unit;

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramControllerTest extends TestCase
{
    public function testSendMessage()
    {
        //arrange
        $chatId = Config::get('telegram.test.chat_id');
        $message = 'Unit Test message';
        $telegram = new TelegramController();

        //act
        $response = $telegram->sendMessage([
            'to' => $chatId,
            'message' => $message
        ]);

        //assert
        $this->assertEquals($response->getText(), $message);
    }
}
