<?php
/**
 * Created by PhpStorm.
 * User: gil
 * Date: 5/30/18
 * Time: 10:23 AM
 */

namespace App\Http\Controllers;

use App\SchedulingMessage;
use App\Util\HandlePostVariables;
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
                'classes' => json_encode($params['waysDelivery']),
                'conditions_stop' => isset($params['conditionsStopDelivery']) ? json_encode($params['conditionsStopDelivery']) : null,
                'conditions_update' => isset($params['conditionsRefreshDelivery']) ? json_encode($params['conditionsRefreshDelivery']) : null,
                'operation' => $params['dateDelivery']['operation'],
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

    /**
     * Update delivery date or stop delivery
     * @param $params
     * @return bool
     */
    public function stopOrRefreshScheduling($params): bool {
        $messages = SchedulingMessage::where('processed', false)
            ->where('conditions_stop', '<>', null)->get();

        $messages->each(function (SchedulingMessage $message) use ($params) {
            $conditionsStop = json_decode($message->conditions_stop, true);
            $condition = new HandlePostVariables($conditionsStop, $params);
            if($condition->handleConditions()) {
                $message->processed = true;
                $message->update();
            }
        });

        $messages = SchedulingMessage::where('processed', false)
            ->where('conditions_update', '<>', null)->get();
        $messages->each(function (SchedulingMessage $message) use ($params) {
            $conditionsUpdate = json_decode($message->conditions_update, true);
            $condition = new HandlePostVariables($conditionsUpdate, $params);
            if($condition->handleConditions()) {
                $message->delivery_date = $this->calcDeliveryDate(Carbon::today()->toString(), $message->operation);
                $message->update();
            }
        });

        return true;
    }
}
