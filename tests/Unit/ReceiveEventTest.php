<?php

namespace Tests\Unit;

use App\Jobs\ReceiveEvent;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReceiveEventTest extends TestCase
{
    protected string $post = '{"header": {"host": "https://mytest.com","messageType": "event"},"body": {"to": "0123456789", "timezone": "São Paulo", "card": "card 1", "user": "Test User", "description": "It is a description", "text": "It is a text", "event": "post.header.messageType","host": "post.header.host"}}';

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
     * @doesNotPerformAssertions
     */
    public function testHandle()
    {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);

        // act
        $receiveEvent->handle();

        // assert
        // it is a no return method
    }

    /**
     * @test
     */
    public function testCheckPostCondition()
    {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);

        // act
        $testTrue = $receiveEvent->checkPostCondition("post.header.host", "https://mytest.com" );
        $testFalse = $receiveEvent->checkPostCondition("post.header.host", "event" );

        // assert
        $this->assertTrue($testTrue);
        $this->assertFalse($testFalse);
    }

    public function testPreparePostVariables() {
        // arrange
        $receiveEvent = new ReceiveEvent($this->post);
        $classes = json_decode(file_get_contents(dirname(__DIR__, 1) . '/config/eventsMap.json'), true)['boardActions'][0]['classes'];

        // assert
        $this->assertNotEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);

        // act
        $receiveEvent->preparePostVariables($classes);

        // assert
        $this->assertEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);
    }
}
