<?php

namespace App\Listeners;

use App\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;

class CalledWebhook
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        // call function by webhook event
        $settings = $event->settings;
        foreach($settings['classes'] as $class){
            $object = new $class['name'];
            foreach($class['methods'] as $action) {
                $method = $action;
                $object->$method($settings);
            }
        }
    }
}
