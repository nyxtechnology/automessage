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
        $settings = $event->settings;
        foreach($settings as $classes) {
            // check if the class exists and instance it
            if (class_exists($classes['class'])) {
                $object = new $classes['class'];
                // loop through the class method
                foreach($classes['methods'] as $methods) {
                    foreach($methods as $method => $parameters){
                        // check if the method exists and call it
                        if (method_exists($object, $method)) {
                            try {
                                $object->$method($parameters);
                            } catch (\Exception $e) {
                                Log::error('CalledWebhook:handle - Error: '.$e->getMessage());
                                continue;
                            }
                        }
                        else
                            Log::error('CalledWebhook:handle - Error: Method '.$method.' does not exist in class '.$classes['class']);
                    }
                }
            }
            else
                Log::error('CalledWebhook:handle - Error: '.$classes['class'].' does not exist');
        }
    }
}
