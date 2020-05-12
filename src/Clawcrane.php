<?php

namespace Iamalexchip;

class Clawcrane
{
    private $haystack = null;
    public $data = null;
    public $errors = [];
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($haystack = null)
    {
        $this->haystack = $haystack;
    }

    public function get($template)
    {
        if (is_array($template)) {
            $template = json_decode(json_encode($template));
        }

        if (is_string($template)) {
            $template = json_decode($template);
        }

        if (is_object($this->haystack) || is_array($this->haystack)) {
            $this->data = $this->grab($template, $this->haystack);
        } else {
            $this->errors[] = 'Haystack must be object or array';
        }

        return $this;
    }

    private function grab($template, $data)
    {
        if (!is_iterable($data)) {
            return $this->pick($template, $data);
        }

        $result = [];

        foreach ($data as $item) {
            $result[] = $this->pick($template, $item);
        }

        return $result;
    }

    private function pick($keys, $target)
    {
        $props = $target->clawcraneProps(); 
        $result = [];

        foreach ($keys as $key => $value) {
            if (!array_key_exists($key, $props)) continue;
            $prop = $props[$key];
            
            // check if can be accessed
            if (array_key_exists('check', $prop) &&!$prop['check']) continue;
            
            $value = $prop['value'];

            if (is_object($keys->{$key})) {
                $result[$key] = $this->grab($keys->{$key}, $value);
                continue;
            }
            
            if (is_object($value)) {
                if ($this->isModel($value) || $this->isIterable($value)) continue;
            }

            $result[$key] = $value;
        }

        return (object) $result;
    }

    /**
     * Checks if an object is a model
     *
     * @param object
     *
     * @return boolean
     */
    public static function isModel($value)
    {
        return $value instanceof \Illuminate\Database\Eloquent\Model;
    }
}
