<?php

namespace Zerochip;

use Zerochip\ClawCrane\Caller;
use Zerochip\ClawCrane\Picker;

class ClawCrane
{
    /**
     * Call the given Eloquent methods on the given object
     *
     * @param array $methods
     * @param object Illuminate\Database\Eloquent\Model
     *
     * @return array
     */
    public static function call($methods, $model)
    {
        $caller = new Caller($methods, $model);
        return $caller->parse();
    }

    /**
     * Get the given attributes in the given object
     *
     * @param array $attributes
     * @param mixed
     *
     * @return array
     */
    public static function pick($attributes, $model)
    {
        $caller = new Picker($attributes, $model);
        return $caller->parse();
    }

    /**
     * Get the given items from the available resources
     *
     * @param array $items
     *
     * @return array
     */
    public static function fetch($requests)
    {
        $data = null;
        $errors = [];

        foreach ($requests as $key => $request) {
            $isValid = true;
            $model = config("clawcrane.resources.{$request['resource']}");

            if (is_null($model)) {
                $errors[] = "{$key}: Unknown Resource [{$request['resource']}] in request";
                $isValid = false;
            } else {
                if (!class_exists($model)) {
                    $errors[] = "Class [$model] does not exist";
                    $isValid = false;
                }
            }

            if (!$isValid) {
                $data[$key] = null;
                continue;
            }

            $caller = new Caller($request['methods'], new $model);
            $callerResult = $caller->parse();

            if (array_key_exists('error', $callerResult)) {
                $errors[] = $callerResult['error'];
            } else {
                $picker = new Picker($request['fetch'], $callerResult['data']);
                $data[$key] = $picker->parse();
            }
        }

        $result['data'] = $data;

        if ($errors) {
            $result['errors'] = $errors;
        }

        return $result;
    }
}
