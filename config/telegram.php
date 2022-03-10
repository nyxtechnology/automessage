<?php

return [

    /*
     * Telegram API token.
     */
    'api_key' => env('TELEGRAM_BOT_TOKEN', null),
    'test' => [
        'chat_id' => env('TELEGRAM_TEST_CHAT_ID', null)
    ],
];
