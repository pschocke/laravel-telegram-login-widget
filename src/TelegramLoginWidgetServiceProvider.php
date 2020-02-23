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

        $this->app->singleton(TelegramLoginWidget::class, function () {
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
    }
}
