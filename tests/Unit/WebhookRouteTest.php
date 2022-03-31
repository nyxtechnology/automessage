<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WebhookRouteTest  extends TestCase
{
    /**
     * @group webhookRoute
     */
    public function testWebhookRouteAPI() {
        //arrange
        $webhookKey = Config::get('settings.webhook_key');
        $data = ['created' => true];
        $url = 'api/webhook/';

        //act
        $responseTrue = $this->post($url . $webhookKey, $data);
        $responseNotFound = $this->post($url, $data);

        //assert
        $responseTrue->assertStatus(200);
        $responseNotFound->assertStatus(404);
    }
}
