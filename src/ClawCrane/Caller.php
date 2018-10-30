<?php

namespace Zerochip\ClawCrane;

use Illuminate\Pagination\Paginator;

class Caller
{
    /**
    * Methods to be executed on model
    *
    * @var object Illuminate\Database\Eloquent\Model
    */
    public $methods;

    /**
    * Model class instance
    *
    * @var object Illuminate\Database\Eloquent\Model
    */
    public $model;

    /**
    * Model class name
    *
    * @var string
    */
    public $modelName;

    /**
     * Check if one of the custom resolvers has error
     *
     * @var boolean
     */
    public $hasError = false;

    /**
     * Error message
     *
     * @var string
     */
    public $errorMessage;

    /**
     * Supported methods
     *
     * @var array
     */
    public $supportedMethods = [
        'get',
        'paginate',
        'simplePaginate',
        'find',
        'first',
        'exists',
        'doesntExist',
        'trashed',
        'count',
        'max',
        'min',
        'avg',
        'sum',
        'skip',
        'offset',
        'take',
        'limit',
        'orderBy',
        'latest',
        'oldest',
        'inRandomOrder',
        'distinct',
        'where',
        'orWhere',
        'whereBetween',
        'whereNotBetween',
        'whereIn',
        'whereNotIn',
        'whereNull',
        'whereNotNull',
        'whereColumn',
        'whereTime',
        'whereDate',
        'whereDay',
        'whereMonth',
        'whereYear',
        'with',
        'withTrashed',
        'onlyTrashed',
    ];

    /**
     * Methods which are not just forwarded to the model for execution
     *
     * @var array
     */
    public $customResolvers = [
        'get',
        'find',
        'paginate',
        'simplePaginate',
    ];

    /**
     * Initialize instance and gets supported methods
     *
     * @return void
     */
    public function __construct($methods, $model)
    {
        $this->methods = $methods;
        $this->model = $model;
        $this->modelName = get_class($this->model);
        $this->config = $this->model->clawCrane();
    }

    /**
     * Calls methods $this->methods) on the model $this->model
     *
     * @param array $query
     *
     * @return mixed
     */
    public function parse()
    {
        if (!$this->config['allow']) {
            return $this->errorMessage('Access denied');
        }

        if (method_exists($this->model, 'scopeClawCraneVisible')) {
            $this->model = $this->model->clawCraneVisible();
        }

        foreach ($this->methods as $params) {
            $method = array_shift($params);

            if (!$this->methodIsValid($method)) {
                break;
            }

            if (in_array($method, $this->customResolvers)) {
                $this->model = $this->$method($params);
            } else {
                $this->model = call_user_func_array([$this->model, $method], $params);
            }
        }

        if ($this->errorMessage) {
            return [
                'data' => null,
                'error' => "{$this->modelName}: {$message}"
            ];
        }

        return [ 'data' => $this->model ];
    }

    /**
     * Checks if a method is valid
     *
     * @param string $method
     *
     * @return boolean
     */
    public function methodIsValid($method)
    {
        $eloquentMethod = false;

        if (in_array($method, $this->supportedMethods)) {
            $eloquentMethod = true;
        }

        if (array_key_exists('methods', $this->config)) {
            if (array_key_exists($method, $this->config['methods'])) {
                if (!$this->config['methods'][$method]) {
                    $this->errorMessage = "Unauthorised method requested [{$method}]";
                    return false;
                }
            } else {
                if (!$eloquentMethod) {
                    $this->errorMessage = "Unknown method requested [{$method}]";
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Resolver: get
     *
     * @return object Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->model->get();
    }

    /**
     * Resolver: find
     *
     * @param array $params
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find($key)
    {
        return $this->model->find($key)->first();
    }

    /**
     * Resolver: pagination
     *
     * @param array $params
     *
     * @return object Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $perPage = $params[0];
        $currentPage = $params[1];

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        return $this->model->paginate($perPage);
    }

    /**
     * Resolver: simplePaginate
     *
     * @param array $params
     *
     * @return object Illuminate\Pagination\Paginator
     */
    public function simplePaginate($params)
    {
        $perPage = $params[0];
        $currentPage = $params[1];

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        
        return $this->model->simplePaginate($perPage);
    }
}
