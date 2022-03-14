<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SendMailController;

class SendMailControllerTest extends TestCase
{
    /**
     * Test to send mail to mailhog and verify if the system is work.
     *
     * @return void
     */
    public function testSendMail()
    {
        $subject = "You're not chasing rainbows - " . time();
        $email = new SendMailController();
        $email->sendMail([
            'name' => 'Oliver Sykes',
            'to' => 'oliver@sykes.com',
            'subject' => $subject,
            'message' => "Dont'go to the house of wolves"
        ]);

        $response = Http::get('http://mailhog:8025/api/v2/search', [
            'kind' => 'to',
            'query' => 'oliver@sykes.com'
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertJson($response->body());
        $this->assertTrue($response->json()["total"] > 0);
        $this->assertTrue($response->json()['items'][0]['Content']['Headers']['Subject'][0] == $subject);
    }
}
