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
}
