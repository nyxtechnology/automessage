<?php

namespace App\Listeners;

use App\Events\WebhookReceived;

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
        foreach($settings['classes'] as $key => $value){
            foreach($value as $action) {
                $method = $action;
                $class = $key;
                $object = new $class();
                $object->$method($settings);
            }
        }
    }
}
