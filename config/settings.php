<?php
return [
    'event_path' => env('EVENT_MAP_PATH', base_path() . '/config/eventsMap.json'),
    'webhook_key' => env('WEBHOOK_KEY', 'secret'),
];
