<?php

namespace NotificationChannels\Twilio;

use Illuminate\Support\ServiceProvider;

class TwilioProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(TwilioChannel::class)
            ->needs(\Services_Twilio::class)
            ->give(function () {
                $config = config('services.twilio');

                return new \Services_Twilio(
                    $config['account_sid'],
                    $config['auth_token']
                );
            });
    }
    
    /**
     * Register the application services.		
     */
    public function register()		
    {		
    }
}
