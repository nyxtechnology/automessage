<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/30/18
 * Time: 10:23 AM
 */

namespace App\Http\Controllers;

use App\SchedulingMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class SchedulingController extends Controller
{
    /**
     * Calculate message delivery date
     * @param string $date
     * @param string $operation
     * @return null|Carbon
     */
    public function calcDeliveryDate(string $date, string $operation): ?Carbon
    {
        try {
            $calcDate = Carbon::parse($date);
            // position 0 is the operator,
            // position 1 is the value and
            // position 2 is type
            $operation = explode(' ', strtolower($operation));
            if (count($operation) !== 3) {
                Log::error('Invalid operation: ' . json_encode($operation));
                return null;
            }

            switch ($operation[2]) {
                case 'days':
                case 'day':
                    $calcDate = $operation[0] == '-' ?
                        $calcDate->subDays($operation[1]) :
                            $calcDate->addDays($operation[1]);
                    break;
                case 'weeks':
                case 'week':
                    $calcDate = $operation[0] == '-' ?
                        $calcDate->subWeeks($operation[1]) :
                            $calcDate->addWeeks($operation[1]);
                    break;
                case 'months':
                case 'month':
                    $calcDate = $operation[0] == '-' ?
                        $calcDate->subMonths($operation[1]) :
                            $calcDate->addMonths($operation[1]);
                    break;
                case 'years':
                case 'year':
                    $calcDate = $operation[0] == '-' ?
                        $calcDate->subYears($operation[1]) :
                            $calcDate->addYears($operation[1]);
                    break;
                default:
                    Log::error('Invalid operation: ' . json_encode($operation));
                    return null;
                    break;
            }

            return $calcDate;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * Save scheduling message to database
     * @param array $params
     * @return null|SchedulingMessage
     */
    public function saveMessage(array $params): ?SchedulingMessage
    {
        $message = null;
        try {
            $message = SchedulingMessage::create([
                'id' => Uuid::uuid1(),
                'data' => json_encode($params['waysDelivery']),
                'conditions_stop' => isset($params['conditionsStopDelivery']) ? json_encode($params['conditionsStopDelivery']) : null,
                'conditions_update' => isset($params['conditionsUpdateDelivery']) ? json_encode($params['conditionsUpdateDelivery']) : null,
                'delivery_date' => $params['deliveryDate']
            ]);
        }
        catch (\Exception $e) {
            Log::error('SchedulingController: saveMessage: ' . $e->getMessage());
        } finally {
            return $message;
        }
    }

    /**
     * Prepare scheduling message
     * @param array $params
     * @return SchedulingMessage|null
     */
    public function messageScheduling(array $params): ?SchedulingMessage
    {
        if (isset($params['dateDelivery'])) {
            if (isset($params['dateDelivery']['startDate'])) {
                $dateDelivery = null;
                if (isset($params['dateDelivery']['operation']))
                    $dateDelivery = $this->calcDeliveryDate($params['dateDelivery']['startDate'], $params['dateDelivery']['operation']);
                else {
                    Log::error('SchedulingController: messageScheduling: operation not found');
                    return null;
                }

                if (!is_null($dateDelivery))
                    $params['deliveryDate'] = $dateDelivery;
                else {
                    Log::error('SchedulingController: messageScheduling: dateDelivery invalid params');
                    return null;
                }

                return $this->saveMessage($params);
            }
            else {
                Log::error('SchedulingController: messageScheduling: startDate not found');
                return null;
            }
        }
        else{
            Log::error('SchedulingController: messageScheduling: dateDelivery not found');
            return null;
        }
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
