<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/30/18
 * Time: 10:23 AM
 */

namespace App\Http\Controllers;

use App\EmailSchedulings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SchedulingController extends Controller
{
    public function saveSchedulingEmails($settings){
        $email = EmailSchedulings::where([
            ['external_id', '=', $settings['params']['extenalId']],
            ['to', '=', $settings['params']['to']],
            ['delivery_date', '=', $settings['params']['deliveryDate']],
            ])->first();
        if($email == null) {
            $email = new EmailSchedulings();
            $email->id = \Ramsey\Uuid\Uuid::uuid1();
        }
        $email->external_id = $settings['params']['extenalId'];
        $email->from = isset($settings['params']['from']) ? $settings['params']['from'] : null;
        $email->from_name = isset($settings['params']['fromName']) ? $settings['params']['fromName'] : null;
        $email->to = $settings['params']['to'];
        $email->to_name = isset($settings['params']['toName']) ? $settings['params']['toName'] : null;
        $email->subject = $settings['params']['subject'];
        $email->body = isset($settings['params']['body']) ? $settings['params']['body'] : null;
        $email->template = isset($settings['params']['template']) ? $settings['params']['template'] : null;
        $email->delivery_date = $settings['params']['deliveryDate'];
        $email->event_stop = isset($settings['params']['eventStop']) ? $settings['params']['eventStop'] : null;
        $variables = '';
        if(isset($settings['params']['templateVariables'])){
            foreach ($settings['params']['templateVariables'] as $key => $value) {
                    $variables .= $key . '*|#-:-#|*' . $value;
                end($settings['params']['templateVariables']);
                if ($key !== key($settings['params']['templateVariables']))
                    $variables .= '*|#-;-#|*';
            }
        }
        $email->template_variables = $variables;
        $email->sent = false;
        $email->save();

        $this->generateLog('Agendamento salvo', $settings['params']['to'], $settings['event'], $email);
    }

    public function deleteSchedulingEmails($settings)
    {
        $emails = EmailSchedulings::where('event_stop', $settings['event'])
            ->where('external_id', isset($settings['params']['extenalId']) ? $settings['params']['extenalId'] : null)
            ->where('to', $settings['params']['to'])
            ->where('sent', false)->get();
        foreach ($emails as $email) {
            try {
                EmailSchedulings::destroy($email->id);
                $this->generateLog('Agendamento deletado', $settings['params']['to'], $settings['event'], $email);
            }
            catch (\Exception $e){
                Log::error('deleteSchedulingEmails - Error: '.$e->getMessage());
                continue;
            }
        }
    }

    /**
     * Deletes all scheduled records from an email by stop event
     * @param $settings
     */
    public function deleteAllSchedulingEmailsByEventStop($settings)
    {
        $emails = EmailSchedulings::where('event_stop', $settings['event'])
            ->where('to', $settings['params']['to'])
            ->where('sent', false)->get();
        foreach ($emails as $email) {
            try {
                EmailSchedulings::destroy($email->id);
                $this->generateLog('Agendamento deletado', $settings['params']['to'], $settings['event'], $email);
            }
            catch (\Exception $e){
                Log::error('deleteSchedulingEmails - Error: '.$e->getMessage());
                continue;
            }
        }
    }

    public function updateSchedulingEmails($settings)
    {
        $emails = EmailSchedulings::where('event_stop', $settings['event']);
            if(isset($settings['params']['extenalId']))
                $emails->where('external_id', $settings['params']['extenalId']);
        $emails = $emails->get();
        foreach ($emails as $email) {
            try {
                $email->delivery_date = $settings['params']['deliveryDate'];
                $email->save();
                $this->generateLog('Agendamento atualizado', $settings['params']['to'], $settings['event'], $email);
            }
            catch (\Exception $e){
                Log::error('updateSchedulingEmails - Error: '.$e->getMessage());
                continue;
            }
        }
    }
}
