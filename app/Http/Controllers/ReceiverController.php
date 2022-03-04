<?php

namespace App\Http\Controllers;

use App\Jobs\ReceiveEvent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ReceiverController extends Controller
{
    public function handleWebhook(Request $request){
        // checks if the event configuration file exists
        if(file_exists(Config::get('settings.event_path'))) {
            // send event to queue
            try {
                ReceiveEvent::dispatch('{"header": ' . json_encode($request->header()) . ', "body": ' . $request->getContent() ."}", true)->onConnection(Config::get('queue.default'));
            } catch (\Exception $e) {
                Log::error('Error send event to queue: ' . $e->getMessage());
                return response('Houston, we have a problem!', 500);
            }
            return response('We have received your event and will process it shortly if everything is ok.', 200);
        }
        Log::error("Event configuration file doesn't exist!");
        return response("Houston, we have a problem! Apparently you don't have an event configuration file.", 500);
    }
}

