<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ReceiverController extends Controller
{
  public function handleWebhook(){
#   json_encode(Storage::disk('local')->get('MailController.json'));
    $path = storage_path('json/MailController.json');
    $json = json_decode($path, true);
  }
}
   
