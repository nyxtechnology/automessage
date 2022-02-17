<?php

namespace App\Http\Controllers;

use App\Events\WebhookReceived;
use App\Jobs\ReceiveEvent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use stdClass;

class ReceiverController extends Controller
{
  public function handleWebhook(Request $request){
      //TODO: migrate event file to db

      // check if event is mapped
      if(!Config::get('eventsMap.' . $request->json('event'))){
          Log::error('Event not mapped: ' . $request->json('event'));
          return response('Event not mapped.', 404);
      }

      $message = new stdClass();
      $message->event = $request->json('event');
      $message->metadata = $request->json('metadata');

      // send event to queue
      try{
          ReceiveEvent::dispatch(json_encode($message))->onConnection(Config::get('queue.default'));
      }catch(\Exception $e){
          Log::error('Error send event to queue: ' . $e->getMessage());
          return response('We have a problem! Try again later.', 500);
      }

      return response('We have received your event and will process it shortly if everything is ok.', 200);
  }
}

