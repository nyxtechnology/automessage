<?php

return [

    /*
     * API endpoint settings.
     *
     */
    'api' => [
        'endpoint' => 'api.mailgun.net',
        'version' => 'v3',
        'ssl' => true
    ],

    /*
     * Domain name registered with MailgunController
     *
     */
    'domain' => 'mg.peer2beer.com.br',

    /*
     * MailgunController (private) API key
     *
     */
    'api_key' => 'key-',

    /*
     * MailgunController public API key
     *
     */
    'public_api_key' => 'pubkey-8bc2afb2e6a29e65b4dadb86e14c1fd5',

    /*
     * You may wish for all e-mails sent with MailgunController to be sent from
     * the same address. Here, you may specify a name and address that is
     * used globally for all e-mails that are sent by this application through MailgunController.
     *
     */
    'from' => [
        'address' => 'contato@peer2beer.com.br',
        'name' => 'peer2beer'
    ],

    /*
     * Global reply-to e-mail address
     *
     */
    'reply_to' => 'contato@peer2beer.com.br',

    /*
     * Force the from address
     *
     * When your `from` e-mail address is not from the domain specified some
     * e-mail clients (Outlook) tend to display the from address incorrectly
     * By enabling this setting, MailgunController will force the `from` address so the
     * from address will be displayed correctly in all e-mail clients.
     *
     * WARNING:
     * This parameter is not documented in the MailgunController documentation
     * because if enabled, MailgunController is not able to handle soft bounces
     *
     */
    'force_from_address' => false,

    /*
     * Testing
     *
     * Catch All address
     *
     * Specify an email address that receives all emails send with MailgunController
     * This email address will overwrite all email addresses within messages
     */
    'catch_all' => 'gilberto.souza@nyx.tc',

    /*
     * Testing
     *
     * MailgunController's testmode
     *
     * Send messages in test mode by setting this setting to true.
     * When you do this, MailgunController will accept the message but will
     * not send it. This is useful for testing purposes.
     *
     * Note: MailgunController DOES charge your account for messages sent in test mode.
     */
    'testmode' => false
];
