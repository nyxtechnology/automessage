<?php

namespace Tests\Unit;

use App\Util\HandlePostVariables;
use Tests\TestCase;

class HandlePostVariablesTest extends TestCase
{
    protected string $post = '{"header": {"host": "https://mytest.com","messageType": "event", "x-forwarded-for": "178.128.181.251"},"body": {"to": "0123456789", "timezone": "São Paulo", "card": "card 1", "user": "Test User", "description": "It is a description", "text": "It is a text", "event": "post.header.messageType","host": "post.header.host"}}';
    protected array $conditions = [
        [
          "post.header.x-forwarded-for" => "178.128.181.251",
          "post.body.description" => "act-completeChecklist",
          "post.body.user" => "gilberto.souza"
        ],
        [
            "post.header.x-forwarded-for" => "178.128.181.251"
        ]
    ];
    protected array $conditionsFalse = [
        [
            "post.header.x-forwarded-for" => "178.128.181.251",
            "post.body.description" => "act-completeChecklist",
            "post.body.user" => "gilberto.souza"
        ],
        [
            "post.header.x-forwarded-for" => "178.128.181.251",
            "post.body.user" => "gilberto.souza"
        ]
    ];

    /**
     * @group handlePostVariables
     */
    public function testGetPostVariableValue()
    {
        // arrange
        $handlePostVariables = new HandlePostVariables($this->conditions, json_decode($this->post));

        // act
        $resultTrue = $handlePostVariables->getPostVariableValue("post.body.description");
        $resultNull = $handlePostVariables->getPostVariableValue("post.body.null");
        $resultSame = $handlePostVariables->getPostVariableValue("description");

        // assert
        $this->assertEquals($resultTrue, "It is a description");
        $this->assertEquals($resultNull, null);
        $this->assertEquals($resultSame, "description");
    }

    /**
     * @group handlePostVariables
     */
    public function testSetPostVariableRecursive() {
        // arrange
        $post = json_decode($this->post);
        $handlePostVariables = new HandlePostVariables($this->conditions, $post);
        $variables = [
            "message" => "post.body.description",
            "to" => "post.body.to",
            "time" => [
                "timeZone" => "post.body.timezone",
            ]
        ];

        // act
        $handlePostVariables->setPostVariableRecursive($variables);

        // assert
        $this->assertEquals($variables["message"], $post->body->description);
        $this->assertEquals($variables["to"], $post->body->to);
        $this->assertEquals($variables["time"]["timeZone"], $post->body->timezone);
    }

    /**
     * @group handlePostVariables
     */
    public function testCheckCondition()
    {
        // arrange
        $post = json_decode($this->post);
        $handlePostVariables = new HandlePostVariables($this->conditions, $post);

        // act
        $testTrue = $handlePostVariables->checkCondition("post.header.host", $post->header->host);
        $testFalse = $handlePostVariables->checkCondition("post.header.host", "post.header.host" );

        // assert
        $this->assertTrue($testTrue);
        $this->assertFalse($testFalse);
    }

    /**
     * @group handlePostVariables
     */
    public function testPreparePostVariables() {
        // arrange
        $handlePostVariables = new HandlePostVariables($this->conditions, json_decode($this->post));
        $classes = json_decode(file_get_contents(dirname(__DIR__, 1) . '/config/eventsMap.json'), true)['boardActions'][0]['messageControllers'];

        // assert
        $this->assertNotEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);

        // act
        $handlePostVariables->prepareClassesVariables($classes);

        // assert
        $this->assertEquals("São Paulo", $classes[1]["methods"][0]["sendMail"]["templateVariables"]["action"]["date"]["timezone"]);
    }

    /**
     * @group handlePostVariables
     */
    public function testHandleConditions() {
        // arrange
        $post = json_decode($this->post);
        $handlePostVariables1 = new HandlePostVariables($this->conditions, $post);
        $handlePostVariables2 = new HandlePostVariables($this->conditionsFalse, $post);

        // act
        $testTrue = $handlePostVariables1->handleConditions();
        $testFalse = $handlePostVariables2->handleConditions();

        // assert
        $this->assertTrue($testTrue);
        $this->assertFalse($testFalse);
    }
}
