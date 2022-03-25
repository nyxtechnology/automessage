<?php

namespace App\Util;

use phpDocumentor\Reflection\Types\This;

class HandlePostVariables
{
    private $message;
    private $conditions;

    public function __construct($conditions, $post)
    {
        $this->message = $post;
        $this->conditions = $conditions;
    }

    public function handleConditions(): bool
    {
        $testConditions = true;
        foreach ($this->conditions as $condition) {
            // condition object works with operator AND
            // so if any condition is false,
            // the whole condition will be false.
            foreach ($condition as $key => $value) {
                if(!$this->checkCondition($key, $value)) {
                    $testConditions = false;
                    break;
                }
                $testConditions = true;
            }
            // conditions array works with operator OR
            // so if any condition item is true,
            // the whole condition will be true.
            if($testConditions)
                break;
        }
        return $testConditions;
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

    /**
     * Get the post variable value
     *
     * @param $value
     * @return mixed
     */
    public function getPostVariableValue(string $variable) {
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
    public function setPostVariableRecursive(&$value) : void {

        if(is_array($value)) {
            foreach ($value as $k => &$v)
                $this->setPostVariableRecursive($v);
        }
        else
            $value = $this->getPostVariableValue($value);
    }
}
