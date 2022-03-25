<?php

namespace Tests\Unit;

use App\Jobs\ReceiveEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReceiveEventTest extends TestCase
{
    protected string $post = '{"header": {"host": "https://mytest.com","messageType": "event", "x-forwarded-for": "178.128.181.251"},"body": {"to": "0123456789", "timezone": "São Paulo", "card": "card 1", "user": "Test User", "description": "It is a description", "text": "It is a text", "event": "post.header.messageType","host": "post.header.host"}}';

    /**
     * @group receiveEvent
     */
    public function testDispatch()
    {
        // arrange
        Event::fake();

        // act
        Event::dispatch(new ReceiveEvent('test'));

        // assert
        Event::assertDispatched(ReceiveEvent::class);
    }

    /**
     * @group receiveEvent
     */
    public function testHandle()
    {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);

        // act
        $count = $receiveEvent->handle();

        // assert
        $this->assertTrue($count == 1);
    }
}
