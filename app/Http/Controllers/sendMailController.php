<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendMailController extends Controller
{
    public function sendMail($settings)
    {
        Mail::send('mails.example', ["body" => $settings['params']['message']], function ($message) use ($settings) {
            $message->to($settings['params']['to'], $settings['params']['name'])->subject($settings['params']['subject']);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}