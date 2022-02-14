<?php

namespace App\Http\Controllers;

use App\Events\WebhookReceived;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ReceiverController extends Controller
{
  public function handleWebhook(Request $request){
      // save request data
      $settings = Config::get('eventsMap.' . $request->json('event'));
      // check if event is mapped
      if(!$settings){
          Log::error('Event not mapped: ' . $request->json('event'));
          return response('Event not mapped.', 404);
      }
      $settings['params'] = $request->json('metadata');
      $settings['event']  = $request->json('event');
      $this->generateLog('Received', $settings['params']['to'], $request->json('event'), $request->json('metadata'));
      // dispatch event
      event(new WebhookReceived($settings));
      return response('We have received your event and will process it shortly if everything is ok.', 200);
  }
}

