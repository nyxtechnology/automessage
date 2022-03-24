<?php

namespace App\Jobs;

use App\Events\WebhookReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ReceiveEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = json_decode($message);
    }

    /**
     * Execute the job.
     *
     * @return int
     */
    public function handle(): int
    {
        $eventMap = json_decode(file_get_contents(Config::get('settings.event_path')),true);
        sleep(2);
        $count = 0;
        foreach ($eventMap as $events) {
            foreach ($events as $event) {
                // check if event's conditions is true
                $conditions = true;
                foreach ($event['conditions'] as $condition) {
                    // condition object works with operator AND
                    // so if any condition is false,
                    // the whole condition will be false.
                    foreach ($condition as $key => $value) {
                        if(!$this->checkCondition($key, $value)) {
                            $conditions = false;
                            break;
                        }
                        $conditions = true;
                    }
                    // conditions array works with operator OR
                    // so if any condition item is true,
                    // the whole condition will be true.
                    if($conditions)
                        break;
                }
                if($conditions) {
                    $this->prepareClassesVariables($event['classes']);
                    event(new WebhookReceived($event['classes']));
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     *  This method prepares class variables
     *  by inserting dynamic values when necessary.
     *
     * @param $classes
     * @return void
     */
    public function prepareClassesVariables(&$classes) {
        // Loop through the classes to get each class
        foreach ($classes as &$class) {
            // Loop through the methods to get each method
            foreach ($class['methods'] as &$method) {
                // Loop through the method to get all params
                foreach ($method as &$params) {
                    // Loop through the params to get each one
                    foreach ($params as $key => &$value) {
                        $this->setPostVariableRecursive($value);
                    }
                }
            }
        }
    }

    /**
     * Set the POST variable value recursively
     *
     * @param $value
     */
    private function setPostVariableRecursive(&$value) : void {
        if(is_array($value)) {
            foreach ($value as $k => &$v)
                $this->setPostVariableRecursive($v);
        }
        else
            $value = $this->getPostVariableValue($value);
    }

    /**
     * Get the post variable value
     *
     * @param $value
     * @return mixed
     */
    private function getPostVariableValue(string $variable) {
        // check if the variable starts with 'post'
        if(strpos($variable, 'post') === 0) {
            $path = explode('.', $variable);
            $value = $this->message;
            for ($i = 1; $i < count($path); $i ++) {
                if (isset($value->{$path[$i]}))
                    $value = $value->{$path[$i]};
                else
                    return null;
            }
            return $value;
        }
        return $variable;
    }

    /**
     * Check if the POST condition is true
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function checkCondition($valueOne, $valueTwo): bool
    {
        $value = $this->getPostVariableValue($valueOne);
        if($value == null)
            return false;
        if (is_array($value) && in_array($valueTwo, $value))
            return true;
        if ($value == $valueTwo)
            return true;
        return false;
    }
}
