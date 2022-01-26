<?php

namespace App\Classes;

use App\Console\Commands\SendMailSMTP;

class SendMailYahoo extends SendMailSMTP {

    public function __construct()
    {
        config(['mail.host' => 'smtp.yahoo.com']);
        config(['mail.port' => '587']);
    }
}