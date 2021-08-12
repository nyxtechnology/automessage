<?php

return [

    /*
     * API endpoint settings.
     *
     */
    'api' => [
        'endpoint' => env('JADLOG_BASE_ENDPOINT', 'www.jadlog.com.br/embarcador/api/'),
        'freight' => env('JADLOG_FREIGHT_SIMULATOR', 'frete/valor'),
        'create_order' => env('JADLOG_CREATE_REQUEST', 'pedido/incluir'),
        'delete_order' => env('JADLOG_DELETE_REQUEST', 'pedido/cancelar'),
        'tracking' => env('JADLOG_TRACKING', 'tracking/consultar'),
    ],

    'checking_account' => env('JADLOG_CHECKINGACCOUNT', 0161351),

    /*
     * JadLog Token
     */
    'api_key' => env('JADLOG_TOKEN','eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOjc4ODE4LCJkdCI6IjIwMTkxMjA1In0.F_Nd5mzGoW_AY7-uv-juvzLD4UQiF0YvrDWF3wfX0ps'),
];
