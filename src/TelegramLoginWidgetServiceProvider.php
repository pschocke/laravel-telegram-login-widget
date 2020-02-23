<?php

namespace pschocke\TelegramLoginWidget;

use Illuminate\Support\ServiceProvider;

class TelegramLoginWidgetServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'pschocke');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'pschocke');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telegramloginwidget.php', 'telegramloginwidget');

        // Register the service the package provides.
        $this->app->singleton('telegramloginwidget', function ($app) {
            return new TelegramLoginWidget;
        });
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/telegramloginwidget.php' => config_path('telegramloginwidget.php'),
        ], 'telegramloginwidget.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/pschocke'),
        ], 'telegramloginwidget.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/pschocke'),
        ], 'telegramloginwidget.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/pschocke'),
        ], 'telegramloginwidget.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
