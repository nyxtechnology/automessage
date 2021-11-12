<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class sendMail extends Controller
{
    public function sendMail(){
        Artisan::call('send:mail');

        return redirect()->away('http://0.0.0.0:8025/');
    }
}
