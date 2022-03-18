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
    /**
     * Calculate message delivery date
     * @param Carbon $date
     * @param string $operation
     * @return Carbon
     */
    public function calcDeliveryDate(string $date, string $operation): Carbon
    {
        try {
            $date = Carbon::parse($date);
            // position 0 is the operator,
            // position 1 is the value and
            // position 2 is type
            $operation = explode(' ', strtolower($operation));
            if (count($operation) !== 3) {
                Log::error('Invalid operation: ' . json_encode($operation));
                return $date;
            }

            switch ($operation[2]) {
                case 'days':
                case 'day':
                    $operation[0] == '-' ?
                        $date->subDays($operation[1]) :
                            $date->addDays($operation[1]);
                    break;
                case 'weeks':
                case 'week':
                    $date = $operation[0] == '-' ?
                        $date->subWeeks($operation[1]) :
                            $date->addWeeks($operation[1]);
                    break;
                case 'months':
                case 'month':
                    $date = $operation[0] == '-' ?
                        $date->subMonths($operation[1]) :
                            $date->addMonths($operation[1]);
                    break;
                case 'years':
                case 'year':
                    $date = $operation[0] == '-' ?
                        $date->subYears($operation[1]) :
                            $date->addYears($operation[1]);
                    break;
                default:
                    Log::error('Invalid operation: ' . json_encode($operation));
                    break;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        } finally {
            return $date;
        }
    }

    public function messageScheduling(array $params)
    {
        if (isset($params['dateDelivery'])) {
            if (isset($params['dateDelivery']['startDate'])) {
                $dateDelivery = Carbon::parse($params['dateDelivery']['startDate']);
                if (isset($params['dateDelivery']['operation']))
                    $dateDelivery = $this->calcDeliveryDate($dateDelivery, $params['dateDelivery']['operation']);
                // TODO: save to database
                //$messageScheduling
            }
            else
                Log::error('SchedulingController: messageScheduling: startDate not found');
        }
        else
            Log::error('SchedulingController: messageScheduling: dateDelivery not found');
    }

    public function saveSchedulingEmails($settings){
        $email = EmailSchedulings::where([
            ['external_id', '=', $settings['extenalId']],
            ['to', '=', $settings['to']],
            ['delivery_date', '=', $settings['deliveryDate']],
            ])->first();
        if($email == null) {
            $email = new EmailSchedulings();
            $email->id = \Ramsey\Uuid\Uuid::uuid1();
        }
        $email->external_id = $settings['extenalId'];
        $email->from = isset($settings['from']) ? $settings['from'] : null;
        $email->from_name = isset($settings['fromName']) ? $settings['fromName'] : null;
        $email->to = $settings['to'];
        $email->to_name = isset($settings['toName']) ? $settings['toName'] : null;
        $email->subject = $settings['subject'];
        $email->body = isset($settings['body']) ? $settings['body'] : null;
        $email->template = isset($settings['template']) ? $settings['template'] : null;
        $email->delivery_date = $settings['deliveryDate'];
        $email->event_stop = isset($settings['eventStop']) ? $settings['eventStop'] : null;
        $variables = '';
        if(isset($settings['templateVariables'])){
            foreach ($settings['templateVariables'] as $key => $value) {
                    $variables .= $key . '*|#-:-#|*' . $value;
                end($settings['templateVariables']);
                if ($key !== key($settings['templateVariables']))
                    $variables .= '*|#-;-#|*';
            }
        }
        $email->template_variables = $variables;
        $email->sent = false;
        $email->save();

        $this->generateLog('Agendamento salvo', $settings['to'], $settings['event'], $email);
    }

    public function deleteSchedulingEmails($settings)
    {
        $emails = EmailSchedulings::where('event_stop', $settings['event'])
            ->where('external_id', isset($settings['extenalId']) ? $settings['extenalId'] : null)
            ->where('to', $settings['to'])
            ->where('sent', false)->get();
        foreach ($emails as $email) {
            try {
                EmailSchedulings::destroy($email->id);
                $this->generateLog('Agendamento deletado', $settings['to'], $settings['event'], $email);
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
            ->where('to', $settings['to'])
            ->where('sent', false)->get();
        foreach ($emails as $email) {
            try {
                EmailSchedulings::destroy($email->id);
                $this->generateLog('Agendamento deletado', $settings['to'], $settings['event'], $email);
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
            if(isset($settings['extenalId']))
                $emails->where('external_id', $settings['extenalId']);
        $emails = $emails->get();
        foreach ($emails as $email) {
            try {
                $email->delivery_date = $settings['deliveryDate'];
                $email->save();
                $this->generateLog('Agendamento atualizado', $settings['to'], $settings['event'], $email);
            }
            catch (\Exception $e){
                Log::error('updateSchedulingEmails - Error: '.$e->getMessage());
                continue;
            }
        }
    }
}
