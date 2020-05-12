<?php

namespace Iamalexchip;

class Clawcrane
{
    /**
     * Object or array to be fetched from.
     *
     * @var mixed
     */
    private $haystack = null;

    /**
     * Property fetching errors.
     *
     * @var array
     */
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

    /**
     * Get the given properties from the haystack
     *
     * @param mixed $template
     *
     * @return array
     */
    public function get($template)
    {
        $data = null;
        $this->errors = [];

        if (is_array($template)) {
            $template = json_decode(json_encode($template));
        }

        if (is_string($template)) {
            $template = json_decode($template);
        }

        if (is_object($this->haystack) || is_array($this->haystack)) {
            $data = $this->grab($template, $this->haystack);
        } else {
            $this->errors[] = 'Haystack must be object or array';
        }

        return [
            'data' => $data,
            'errors' => $this->errors
        ];
    }

    /**
     * Get the given properties from an object or iterable value
     *
     * @param object $template
     * @param object $target
     *
     * @return array
     */
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

    /**
     * Get the given properties from target
     *
     * @param object $keys properties to fetch
     * @param object $target  
     *
     * The $target can be a \Illuminate\Database\Eloquent\Model or any object with
     * a clawcraneProps() method.
     *
     * @return array
     */
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

        return $result;
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

    /**
     * Checks if a object can be iterated
     * Checks if an object is a model
     *
     * @param mixed
     * @param object
     *
     * @return boolean
     */
    public static function isIterable($value)
    {
        if (is_array($value)) return true;

        return in_array(get_class($value), [
            'Illuminate\Database\Eloquent\Collection',
            'Illuminate\Pagination\LengthAwarePaginator',
            'Illuminate\Pagination\Paginator'
        ]);
    }
}
