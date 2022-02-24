<?php

namespace App\Jobs;

use App\Events\WebhookReceived;
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
     * @return void
     */
    public function handle()
    {
        $eventMap = json_decode(file_get_contents(Config::get('settings.event_path')),true);
        sleep(2);
        try {
            $settings = $eventMap[$this->message->event];
            $settings['params'] = json_decode(json_encode($this->message->metadata), true);
            event(new WebhookReceived($settings));
        } catch (\Exception $e) {
            Log::error($e->getCode() . " - " . $e->getMessage());
        }
    }
}
