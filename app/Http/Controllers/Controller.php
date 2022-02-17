<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function generateLog ($type, $user, $event, $metadata) {
        Log::info('['.$type.'] To = '.$user.', Event = '.json_encode($event).PHP_EOL.'[stacktrace]'.PHP_EOL.print_r($metadata, true));
    }
}
