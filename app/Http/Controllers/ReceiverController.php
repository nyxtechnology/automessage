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
        if(file_exists(Config::get('settings.event_path'))){
            $events = json_decode(file_get_contents(Config::get('settings.event_path')),true);
            // check if event is mapped
            if(!array_key_exists($request->json('event'), $events)){
                Log::error('Event not mapped: ' . $request->json('event'));
                return response('Sorry! This event is not mapped.', 404);
            }
            // send event to queue
            try{
                ReceiveEvent::dispatch($request->getContent(), true)->onConnection(Config::get('queue.default'));
            }catch(\Exception $e){
                Log::error('Error send event to queue: ' . $e->getMessage());
                return response('Houston, we have a problem!', 500);
            }
            return response('We have received your event and will process it shortly if everything is ok.', 200);
        }
    }
}

