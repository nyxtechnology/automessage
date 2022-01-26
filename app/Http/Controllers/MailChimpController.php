<?php

namespace App\Http\Controllers;

use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\NewsletterFacade as Newsletter;

class MailChimpController extends Controller
{
    private $mailchimp;

    /**
     * MailChimpController constructor.
     */
    public function __construct(){
        $this->mailchimp = new MailChimp(Config::get('mailchimpP2B.api_key'));
    }

    public function subscribeList($settings){
        $list_id = Config::get('mailchimpP2B.lists.' . $settings['params']['project'] . '.id');
        $this->mailchimp->post("lists/$list_id/members", [
            'email_address' => $settings['params']['to'],
            'merge_fields' =>
                [
                    'MMERGE5' => $settings['params']['name'],
                    'MMERGE6' => $settings['params']['profile'],
                    'MMERGE11' => $settings['params']['state'],
                    'MMERGE13' => $settings['params']['city']
                ],
            'status' => 'subscribed',
        ]);
        if (!$this->mailchimp->success())
            Log::error('MailChimpController -> subscribeList() ' . $this->mailchimp->getLastError());
    }

    public function updateMemberSubscribeList($settings){
        $listId = Config::get('mailchimpP2B.lists.' . $settings['params']['project'] . '.id');
        $subscriberHash = $this->mailchimp->subscriberHash($settings['params']['oldMail']);
        $this->mailchimp->patch("/lists/$listId/members/$subscriberHash", [
            'merge_fields' =>
                [
                    'MMERGE5' => $settings['params']['name'],
                    'MMERGE6' => $settings['params']['profile'],
                    'MMERGE11' => $settings['params']['state'],
                    'MMERGE13' => $settings['params']['city'],
                    'MERGE0' => $settings['params']['to']
                ]
        ]);
        if (!$this->mailchimp->success())
            Log::error('MailChimpController -> updateMemberSubscribeList() ' . $this->mailchimp->getLastError());
    }
}
