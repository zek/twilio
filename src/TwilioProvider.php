<?php

namespace NotificationChannels\Twilio;

use Services_Twilio as TwilioService;
use Illuminate\Support\ServiceProvider;

class TwilioProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(TwilioChannel::class)
            ->needs(Twilio::class)
            ->give(function () {
                $config = $this->app['config']['services.twilio'];

                return new Twilio(
                    $this->app->make(TwilioService::class),
                    $config['from']
                );
            });

        $this->app->bind(TwilioService::class, function () {
            $config = $this->app['config']['services.twilio'];

            return new TwilioService($config['account_sid'], $config['auth_token']);
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
