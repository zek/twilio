<?php

namespace NotificationChannels\Twilio;

use Illuminate\Support\ServiceProvider;
use Services_Twilio as TwilioService;

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
                $config = $this->app->make(TwilioConfig::class);
                $twilio = $this->app->make(TwilioService::class, [
                    $config->getAccountSid(),
                    $config->getAuthToken(),
                ]);

                return new Twilio($twilio, $config);
            });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(TwilioConfig::class, function () {
            return new TwilioConfig($this->app['config']['services.twilio']);
        });
    }
}
