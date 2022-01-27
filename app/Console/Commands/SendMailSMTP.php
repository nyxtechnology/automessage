<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\mailTest;
use Illuminate\Support\Facades\Mail;
use app\Classes\SendMailYahoo;

class SendMailSMTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Send mail
        $to_name = 'David';
        $to_email = 'Email@teste.com';
        $data = array('name'=>"Sam Jose", "body" => "Test mail");

        Mail::send('mails.example', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Artisans Web Testing Mail');
            $message->from(env('MAIL_FROM_ADDRESS'), 'Artisans Web');
        });
    }
}
