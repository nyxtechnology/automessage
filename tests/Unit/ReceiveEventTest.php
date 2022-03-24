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
     * @group receiveEvent1
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

    /**
     * @group receiveEvent
     */
    public function testCheckCondition()
    {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);

        // act
        $testTrue = $receiveEvent->checkCondition("post.header.host", "https://mytest.com" );
        $testFalse = $receiveEvent->checkCondition("post.header.host", "event" );

        // assert
        $this->assertTrue($testTrue);
        $this->assertFalse($testFalse);
    }

    /**
     * @group receiveEvent
     */
    public function testPreparePostVariables() {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);
        $classes = json_decode(file_get_contents(dirname(__DIR__, 1) . '/config/eventsMap.json'), true)['boardActions'][0]['classes'];

        // assert
        $this->assertNotEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);

        // act
        $receiveEvent->prepareClassesVariables($classes);

        // assert
        $this->assertEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);
    }
}
