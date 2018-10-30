<?php

namespace Zerochip\ClawCrane;

class Picker
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * Target object
     *
     * @var object
     */
    private $target;

    /**
     * @var array
     */
    private $errorMessages = [];

    /**
     * Initialize instance and gets supported methods
     *
     * @return void
     */
    public function __construct($attributes, $target)
    {
        $this->attributes = $attributes;
        $this->target = $target;
    }

    /**
     * Gets $this->attributes from $this->target object
     *
     * @param array $template
     * @param mixed $this->target
     *
     * @return array
     */
    public function parse()
    {
        if (!is_object($this->target)) {
            return [ 'data' => $this->target ];
        }

        if (is_a($this->target, 'Illuminate\Database\Eloquent\Builder')) {
            return [
                'data' => null,
                'errors' => [ "query incomplete" ]
            ];
        }

        $meta = null;
        
        if ($this->is_pagination($this->target)) {
            $meta = [
                'count' => $this->target->count(),
                'hasMorePages' => $this->target->hasMorePages(),
                'per_page' => $this->target->perPage(),
                'current_page' => $this->target->currentPage(),
            ];

            if (is_a($this->target, 'Illuminate\Pagination\LengthAwarePaginator')) {
                $meta['last_page'] = $this->target->lastPage();
                $meta['total'] = $this->target->total();
            }
        }

        $result['data'] = $this->fetch($this->attributes, $this->target);
         
        if ($meta) {
            $result['meta'] = $meta;
        }

        if (count($this->errorMessages) > 0) {
            $result['errors'] = $this->errorMessages;
        }
        
        return $result;
    }

    /**
     * Push a new error message
     *
     * @param string $message
     *
     * @return void
     */
    public function pushError($message)
    {
        if (!in_array($message, $this->errorMessages)) {
            $this->errorMessages[] = $message;
        }
    }

    /**
     * Checks if a object can be iterated
     *
     * @param mixed
     *
     * @return boolean
     */
    private function is_loopable($instance)
    {
        return in_array(get_class($instance), [
            'Illuminate\Database\Eloquent\Collection',
            'Illuminate\Pagination\LengthAwarePaginator',
            'Illuminate\Pagination\Paginator'
        ]);
    }

    /**
     * Checks if pagination object
     *
     * @param object
     *
     * @return boolean
     */
    private function is_pagination($instance)
    {
        return in_array(get_class($instance), [
            'Illuminate\Pagination\LengthAwarePaginator',
            'Illuminate\Pagination\Paginator'
        ]);
    }

    /**
     * Fetch requested keys from an object
     *
     * @param array $attributes
     *
     * @param object $instance
     *
     * @return array
     */
    private function fetch($attributes, $instance)
    {
        $result = null;

        if (is_null($instance)) {
            return null;
        }

        if ($this->is_loopable($instance)) {
            if (!$instance->count()) {
                return [];
            }

            foreach ($instance as $item) {
                $result[] = $this->getAttributes($attributes, $item);
            }
        } else {
            $result = $this->getAttributes($attributes, $instance);
        }

        return $result;
    }

    /**
     * Gets given attributes from a model instance
     *
     * @param array $attributes
     *
     * @param object $instance
     *
     * @return array
     */
    private function getAttributes($attributes, $instance)
    {
        $result = null;
        $className = get_class($instance);
        $availableAttributes = $instance->clawCrane()['attributes'];

        foreach ($attributes as $key => $value) {
            if (!array_key_exists($key, $availableAttributes)) {
                $this->pushError("{$className}: unknown attribute [{$key}]");
                continue;
            }

            if (!$availableAttributes[$key]) {
                $this->pushError("{$className}: attribute [{$key}] is not public");
                continue;
            }

            $attribute = $instance->$key;
            
            if (is_array($value)) {
                if (empty($value)) {
                    $result[$key] = null;
                } else {
                    $result[$key] = $this->fetch($value, $attribute);
                }
            } else {
                if (is_object($attribute)) {
                    if ($this->is_loopable($attribute)) {
                        $result[$key] = $attribute->count();
                    } else {
                        $result[$key] = true;
                    }
                } else {
                    $result[$key] = $attribute;
                }
            }
        }

        return $result;
    }
}
