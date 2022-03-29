<?php

namespace App\Jobs;

use App\Events\WebhookReceived;
use App\Http\Controllers\SchedulingController;
use App\SchedulingMessage;
use App\Util\HandlePostVariables;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ReceiveEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = json_decode($message);
    }

    /**
     * Execute the job.
     *
     * @return int
     */
    public function handle(): int
    {
        $eventMap = json_decode(file_get_contents(Config::get('settings.event_path')),true);
        sleep(2);

        //stop or refresh schedule messages
        $schedule = new SchedulingController();
        $schedule->stopOrRefreshScheduling($this->message);

        $count = 0;
        foreach ($eventMap as $events) {
            foreach ($events as $event) {
                $handlePost = new HandlePostVariables($event['conditions'], $this->message);
                if($handlePost->handleConditions()) {
                    $handlePost->prepareClassesVariables($event['messageControllers']);
                    event(new WebhookReceived($event['messageControllers']));
                    $count++;
                }
            }
        }
        return $count;
    }
}
