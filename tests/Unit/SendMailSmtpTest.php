<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class SendMailControllerTest extends TestCase
{
    /**
     * Test to send mail to mailhog and verify if the system is work.
     *
     * @return void
     */

    public function test_send_mail()
    {
        $subject = "You're not chasing rainbows - " . time();
        $response = $this->postJson('/api/webhook',
        ['event' => "sendMailSMTP",
         'metadata' =>  [
                'name' => 'Oliver Sykes',
                'to' => 'Oliver@sykes.com',
                'subject' => $subject,
                'message' => "Don't go to the house of wolves"]]);

        $response->assertStatus(200);

        $response = Http::get('http://mailhog:8025/api/v2/search', [
            'kind' => 'to',
            'query' => 'Oliver@sykes.com'
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertJson($response->body());
        $this->assertTrue($response->json()["total"] > 0);
        $this->assertTrue($response->json()['items'][0]['Content']['Headers']['Subject'][0] == $subject);
    }
}
