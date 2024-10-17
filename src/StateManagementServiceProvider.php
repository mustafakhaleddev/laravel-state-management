<?php

namespace MKD\StateManagement;

use Illuminate\Support\ServiceProvider;
use MKD\StateManagement\Commands\CreateStoreCastCommand;
use MKD\StateManagement\Commands\CreateStoreCommand;
use MKD\StateManagement\Contracts\StoreContract;
use Mkdev\LaravelAdvancedOTP\Commands\MagicOTPCommand;

class StateManagementServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('state-management.php'),
            ], 'config');


        }

        $this->commands([
            CreateStoreCommand::class,
            CreateStoreCastCommand::class
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {

        // Register the main class to use with the facade
        $this->app->singleton('state-management', function () {
            return new StateManagement();
        });

    }
}
