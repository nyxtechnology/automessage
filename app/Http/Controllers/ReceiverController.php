<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ReceiverController extends Controller
{
  public function handleWebhook(Request $request){
    $request = storage_path('json/MailController.json');
    $json = json_decode($request, true);
    return response("OK");
  }
}
   
