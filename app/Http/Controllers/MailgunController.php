<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/4/18
 * Time: 1:58 PM
 */

namespace App\Http\Controllers;
use App\Util\HandleStrings;
use Hamcrest\Util;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use \Mailgun\Mailgun;

class MailgunController extends Controller
{

    private $mailgun;

    /**
     * MailgunController constructor.
     */
    public function __construct(){
        $this->mailgun = Mailgun::create(Config::get('mailgunP2B.api_key'));
    }

    /**
     * Subscribe in mailing list
     * @param $settings
     */
    public function subscribeList($settings){
        try {
            $settings = $this->prepareString($settings);
            //if not exist list, create
            if (!$this->existList($settings['params']['project']))
                $this->createList($settings['params']['project']);
            //subscriber
            $this->addMailList($settings);
        }
        catch (\Exception $exception){
            Log::error('MailgunController -> subscribeList() ' . $exception->getMessage());
        }
    }

    /**
     * Send email
     * @param $settings
     */
    public function sendEmailTemplate($settings){
        //get variables to template render
        $var = [];
        foreach ($settings['params'] as $param => $render){
            if(is_array($render)){
                foreach ($render as $key => $value)
                    $var[HandleStrings::removeAccents($key)] = $value;
            }
            else
                $var[HandleStrings::removeAccents($param)] = $render;
        }
        //render variables in template use API Mandrill
        $mandrill = new MandrillController();
        $template = $mandrill->renderTemplate($settings['params']['template'], $var);
        //send message use API Mailgun
        $this->mailgun->messages()->send(Config::get('mailgunP2B.domain'), [
            'from'    => Config::get('mailgunP2B.from.address'),
            'to'      => $settings['params']['to'],
            'subject' => $settings['params']['subject'],
            'html'    => $template,
        ]);
        $this->generateLog('Email enviado', $settings['params']['to'], array_key_exists('event', $settings) ? $settings['event'] : 'sendEmailTemplate', $settings);
    }

    /**
     * Get if exist list
     * @param $address
     * @return boolean
     */
    private function existList($address){
        $lists = $this->getAllLists();
        foreach ($lists->http_response_body->items as $key => $list){
            if(strcasecmp($list->address, $address.'@'.Config::get('mailgunP2B.domain')) == 0)
                return true;
        }
        return false;
    }

    /**
     * @param $settings
     * @return \stdClass
     */
    private function addMailList($settings){
        $var = [];
        foreach ($settings['params'] as $param => $render){
            if(is_array($render)){
                foreach ($render as $key => $value)
                    $var[HandleStrings::removeAccents($key)] = $value;
            }
            else
                $var[HandleStrings::removeAccents($param)] = $render;
        }
        $param = json_encode($var);
        $this->mailgun->post('lists/'.$settings['params']['project'].'@'.Config::get('mailgunP2B.domain').'/members', array(
            'address'     => $settings['params']['to'],
            'name'        => $settings['params']['name'],
            'subscribed'  => true,
            'vars'        => $param
        ));
    }

    /**
     * Update a mailing list member
     * @param $settings
     * @return \stdClass
     */
    public function updateMailList($settings){
        if($this->getMemberList($settings)) {
            $settings = $this->prepareString($settings);
            $var = [];
            foreach ($settings['params'] as $param => $render) {
                if (is_array($render)) {
                    foreach ($render as $key => $value)
                        $var[HandleStrings::removeAccents($key)] = $value;
                } else
                    $var[HandleStrings::removeAccents($param)] = $render;
            }
            $param = json_encode($var);
            $this->mailgun->put('lists/' . $settings['params']['project'] . '@' . Config::get('mailgunP2B.domain') . '/members/' . $settings['params']['oldMail'], array(
                'address' => $settings['params']['to'],
                'name' => $settings['params']['name'],
                'vars' => $param,
                'subscribed' => isset($settings['params']['subscribed']) ? $settings['params']['subscribed'] : 'no'
            ));
        }
        else
            $this->subscribeList($settings);
    }

    /**
     * Retrieves a mailing list member
     * @param $settings
     * @return bool
     */
    private function getMemberList($settings){
        $response = false;
        try{
            $this->mailgun->get(
                'lists/' . $settings['params']['project'] .
                '@' . Config::get('mailgunP2B.domain') .
                '/members/' . $settings['params']['oldMail']
            );
            $response = true;
        }catch (\Exception $e){
            Log::error($e->getMessage());
        }finally{
            return $response;
        }
    }

    /**
     * Get all lists
     * @param $limit
     * @return \stdClass
     */
    private function getAllLists($limit = 100){
        return $this->mailgun->get("lists/pages", array(
            'limit'      =>  $limit
        ));
    }

    /**
     * Creates a new mailing list.
     * @param $listName
     * @return \stdClass
     */
    private function createList($listName){
        return $this->mailgun->post("lists", array(
            'address'     => $listName.'@'.Config::get('mailgunP2B.domain')
        ));
    }

    private function prepareString($settings){
        $settings['params']['project'] = HandleStrings::removeSpace($settings['params']['project']);
        $settings['params']['project'] = HandleStrings::removeAccents($settings['params']['project']);
        return($settings);
    }
}
