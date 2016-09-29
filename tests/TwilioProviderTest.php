<?php

namespace NotificationChannels\Twilio\Test;

use Illuminate\Contracts\Foundation\Application;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioConfig;
use NotificationChannels\Twilio\TwilioProvider;
use Services_Twilio as TwilioService;
use NotificationChannels\Twilio\Twilio;
use ArrayAccess;

class TwilioProviderTest extends MockeryTestCase
{
    /** @var TwilioProvider */
    protected $provider;

    /** @var App */
    protected $app;

    public function setUp()
    {
        parent::setUp();

        $this->app = Mockery::mock(App::class);
        $this->provider = new TwilioProvider($this->app);

        //$this->app->shouldReceive('make')->once()->andReturn(Mockery::mock(TwilioService::class));
        //$this->app->shouldReceive('flush');
    }

    /** @test */
    public function it_gives_an_instantiated_twilio_object_when_the_channel_asks_for_it()
    {
        $configArray = [
            'account_sid' => 'sid',
            'auth_token' => 'token',
            'from' => 'from',
        ];
        $twilio = Mockery::mock(TwilioService::class);
        $config = Mockery::mock(TwilioConfig::class, $configArray);

        $this->app->shouldReceive('offsetGet')
            ->once()
            ->with('config')
            ->andReturn([
                'services.twilio' => $configArray
            ]);

        $this->app->shouldReceive('make')->with(TwilioConfig::class, $configArray)->andReturn($config);

        $config->shouldReceive('getAccountSid')->once()->andReturn($configArray['account_sid']);
        $config->shouldReceive('getAuthToken')->once()->andReturn($configArray['auth_token']);

        $this->app->shouldReceive('make')->with(TwilioService::class, [
            $configArray['account_sid'],
            $configArray['auth_token']
        ])->andReturn($twilio);

        $this->app->shouldReceive('when')->with(TwilioChannel::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('needs')->with(Twilio::class)->once()->andReturn($this->app);
        $this->app->shouldReceive('give')->with(Mockery::on(function ($twilio) {
            return $twilio() instanceof Twilio;
        }))->once();

        $this->provider->boot();
    }
}

interface App extends Application, ArrayAccess
{
}
