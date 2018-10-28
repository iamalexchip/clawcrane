<?php

namespace Zerochip;

use Zerochip\ClawCrane\Caller;

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
}
