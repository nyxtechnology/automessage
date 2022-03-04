<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function sendMail($settings)
    {
        $templateVariables = $settings['templateVariables'] ?? [];
        isset($settings['message']) ? $templateVariables["body"] = $settings['message'] : null;
        Mail::send($settings['template'] ?? 'mails.example',
            $templateVariables,
            function ($message) use ($settings) {
                $message->to($settings['to'], $settings['name'] ?? null)->subject($settings['subject']);
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            }
        );
    }
}
