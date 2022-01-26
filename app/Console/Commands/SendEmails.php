<?php

namespace App\Console\Commands;

use App\EmailSchedulings;
use App\Http\Controllers\MailgunController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled emails of the day';

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
        $date = Carbon::now('America/Sao_Paulo');
        $this->info('send:scheduledEmails - Start: '.$date);
        $emails = EmailSchedulings::where('delivery_date', $date)->where('sent', false)->get();
        $this->info('send:scheduledEmails - Amount of email to send today: '.count($emails));
        foreach ($emails as $email) {
            try {
                $settings = [
                    'params' => [
                        'template' => $email->template,
                        'to' => $email->to,
                        'subject' => $email->subject,
                    ]
                ];
                if ($email->template_variables != null) {
                    $values = explode('*|#-;-#|*', $email->template_variables);
                    foreach ($values as $items) {
                        $keyValue = explode('*|#-:-#|*', $items);
                        $settings['params'][$keyValue[0]] = $keyValue[1];
                    }
                }
                $emailGun = new MailgunController();
                $emailGun->sendEmailTemplate($settings);
            }
            catch (\Exception $e){
                $this->error('send:scheduledEmails - Error: '.$e->getMessage());
                continue;
            }
            $email->sent = true;
            $email->save();
            $this->info('send:scheduledEmails - Email ' . $email->template . ' referring to the external id ' . $email->external_id . ' sent.');
        }
        $this->info('send:scheduledEmails - End: '.$date);
    }
}
