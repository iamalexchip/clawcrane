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
        if (is_object($this->haystack) || is_array($this->haystack)) {
            $this->data = $this->grab($template, $this->haystack);
        } else {
            $this->errors[] = 'Haystack must be object or array';
        }

        return $this;
    }

    private function grab($template, $data)
    {
        if (!$this->iterable($data)) {
            if (!$this->canView($data)) return (object) [];
            return $this->pick($template, $data);
        }

        $result = [];

        foreach ($data as $item) {
            if (!$this->canView($item)) continue;
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
            
            // middlware check
            if (array_key_exists('check', $prop) &&!$prop['check']) continue;
            
            if (is_object($keys->{$key})) {
                $result[$key] = $this->grab($keys->{$key}, $prop['value']);
            } else {
                $result[$key] = $prop['value'];
            }
        }

        return (object) $result;
    }

    /**
     * Checks if a object can be iterated
     *
     * @param mixed
     *
     * @return boolean
     */
    public static function iterable($value)
    {
        if (is_array($value)) return true;

        return in_array(get_class($value), [
            'Illuminate\Database\Eloquent\Collection',
            'Illuminate\Pagination\LengthAwarePaginator',
            'Illuminate\Pagination\Paginator'
        ]);
    }

    public function canView($item)
    {
        if (method_exists($item, 'clawcraneAccess')) {
            $access = $item->clawcraneAccess();

            if (!$access['check']) {
                $this->pushError($access['message']);
                return false;
            }

            return true;
        }

        return true;
    }
}
