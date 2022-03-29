<?php

namespace App\Console\Commands;

use App\EmailSchedulings;
use App\Http\Controllers\MailgunController;
use App\SchedulingMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Send the day's scheduled messages";

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
        $date = Carbon::now();
        $this->info('send:scheduledMessages - Start: '.$date);
        // Get all the scheduled messages for today and loop through them
        $messages = SchedulingMessage::where('delivery_date', Carbon::today())
            ->where('processed', false)->get();
        $this->info('send:scheduledMessages - Amount of messages to send today: '.count($messages));
        foreach ($messages as $message) {
            try {
                // transform json from db data to an array and loop through it
                $data = json_decode($message->classes, true);
                foreach($data as $classes){
                    // check if the class exists and instance it
                    if (class_exists($classes['controller'])) {
                        $object = new $classes['controller'];
                        // loop through the class method
                        foreach($classes['methods'] as $methods) {
                            foreach($methods as $method => $parameters){
                                // check if the method exists and call it
                                if (method_exists($object, $method)) {
                                    try {
                                        $object->$method($parameters);
                                    } catch (\Exception $e) {
                                        $this->error('send:scheduledMessages - Error: '.$e->getMessage());
                                        continue;
                                    }
                                }
                                else
                                    $this->error('send:scheduledMessages - Method '.$method.' does not exist in class '.$classes['controller']);
                            }
                        }
                    }
                    else
                        $this->error('send:scheduledMessages - Error: '.$classes['controller'].' does not exist');
                }
            } catch (\Exception $e) {
                $this->error('send:scheduledMessages - Error: ' . $e->getMessage());
                continue;
            } finally {
                $message->processed = true;
                $message->update();
                $this->info("send:scheduledMessages - Message processed: $message->id");
            }
        }
        $this->info('send:scheduledMessages - End: '.$date);
    }
}
