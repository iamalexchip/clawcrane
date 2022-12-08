<?php

namespace Iamalexchip;

use stdClass;

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

    public $keyPath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($haystack = null)
    {
        if (!is_object($haystack) || !is_array($haystack)) {
            $this->errors[] = 'Haystack must be object or array';
        }
        $this->haystack = $haystack;
    }

    /**
     * Get the given properties from the haystack
     *
     * @param mixed $template
     *
     * @return \Iamalexchip\Clawcrane
     */
    public static function from($haystack)
    {
        return new Clawcrane($haystack);
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
        $this->errors = [];

        if (is_array($template)) {
            $template = json_decode(json_encode($template));
        }

        if (is_string($template)) {
            $template = json_decode($template);
        }

        $data = $this->grab($template, $this->haystack);

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
    private function grab($template, $data, $keyPath = "")
    {
        $this->keyPath = $keyPath;

        if (!is_iterable($data)) {
            return $this->pick($template, $data);
        }

        $result = [];

        foreach ($data as $item) {
            $this->keyPath = $keyPath;
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
        $result = new stdClass;

        foreach ($keys as $key => $keyValue) {
            if (!array_key_exists($key, $props)) continue;
            $prop = $props[$key];
            $path = $this->keyPath ? $this->keyPath . '.' . $key : $key;
            $value = $prop['value'];

            // check if can be accessed
            if (array_key_exists('check', $prop) && !$prop['check']) {
                $this->errors[] = '[' . $path . ']: access denied';
                continue;
            }

            // if key has sub props
            if (is_object($keyValue)) {
                $result->{$key} = $this->grab($keyValue, $value, $path);
                continue;
            }

            if (is_object($value)) {
                $this->errors[] = '[' . $path . ']: value is object or array so it cannot be returned';
                continue;
            }

            $result->{$key} = $value;
        }

        return $result;
    }
}
