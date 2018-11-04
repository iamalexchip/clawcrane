<?php

namespace Zerochip;

use Illuminate\Support\ServiceProvider;

class ClawCraneServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // config
        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'laravel-clawcrane');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
