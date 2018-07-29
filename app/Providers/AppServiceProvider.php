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
        $this->publishes([
            __DIR__.'/../../resources/lang' => resource_path('lang/vendor/email-verification'),
        ], 'translations');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'email-verification');
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/email-verification'),
        ], 'views');
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
