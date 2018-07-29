<?php

namespace Atnic\EmailVerification\Providers;

use Illuminate\Support\ServiceProvider;
use Atnic\EmailVerification\Console\Commands\EmailVerificationMakeCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'email-verification');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'email-verification');
        if ($this->app->runningInConsole()) {
            $this->commands([
                EmailVerificationMakeCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
