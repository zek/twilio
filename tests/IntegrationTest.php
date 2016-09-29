<?php

namespace NotificationChannels\Twilio\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Twilio\TwilioCallMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioConfig;
use NotificationChannels\Twilio\TwilioSmsMessage;
use PHPUnit_Framework_TestCase;
use NotificationChannels\Twilio\Twilio;
use Services_Twilio as TwilioService;
use Services_Twilio_Rest_Calls;
use Services_Twilio_Rest_Messages;

class IntegrationTest extends PHPUnit_Framework_TestCase
{
    /** @var TwilioService */
    protected $twilioService;

    /** @var Notification */
    protected $notification;

    /** @var Dispatcher */
    protected $events;

    public function setUp()
    {
        parent::setUp();

        $this->twilioService = Mockery::mock(TwilioService::class);
        $this->twilioService->account = new \stdClass();
        $this->twilioService->account->messages = Mockery::mock(Services_Twilio_Rest_Messages::class);
        $this->twilioService->account->calls = Mockery::mock(Services_Twilio_Rest_Calls::class);

        $this->events = Mockery::mock(Dispatcher::class);
        $this->notification = Mockery::mock(Notification::class);
    }

    /** @test */
    public function it_can_send_a_sms_message()
    {
        $message = TwilioSmsMessage::create('Message text');
        $this->notification->shouldReceive('toTwilio')->andReturn($message);

        $config = new TwilioConfig([
            'from' => '+31612345678'
        ]);
        $twilio = new Twilio($this->twilioService, $config);
        $channel = new TwilioChannel($twilio, $this->events);

        $this->smsMessageWillBeSentToTwilioWith('+31612345678', '+22222222222', 'Message text', null, []);

        $channel->send(new NotifiableWithAttribute(), $this->notification);
    }

    /** @test */
    public function it_can_send_a_sms_message_using_service()
    {
        $message = TwilioSmsMessage::create('Message text');
        $this->notification->shouldReceive('toTwilio')->andReturn($message);

        $config = new TwilioConfig([
            'from' => '+31612345678',
            'sms_service_sid' => '0123456789'
        ]);
        $twilio = new Twilio($this->twilioService, $config);
        $channel = new TwilioChannel($twilio, $this->events);

        $this->smsMessageWillBeSentToTwilioWith('+31612345678', '+22222222222', 'Message text', null, [
            'MessagingServiceSid' => '0123456789'
        ]);

        $channel->send(new NotifiableWithAttribute(), $this->notification);
    }

    /** @test */
    public function it_can_send_a_sms_message_using_alphanumeric_sender()
    {
        $message = TwilioSmsMessage::create('Message text');
        $this->notification->shouldReceive('toTwilio')->andReturn($message);

        $config = new TwilioConfig([
            'from' => '+31612345678',
            'alphanumeric_sender' => 'TwilioTest'
        ]);
        $twilio = new Twilio($this->twilioService, $config);
        $channel = new TwilioChannel($twilio, $this->events);

        $this->smsMessageWillBeSentToTwilioWith('TwilioTest', '+33333333333', 'Message text', null, []);

        $channel->send(new NotifiableWithAlphanumericSender(), $this->notification);
    }

    /** @test */
    public function it_can_make_a_call()
    {
        $message = TwilioCallMessage::create('http://example.com');
        $this->notification->shouldReceive('toTwilio')->andReturn($message);

        $config = new TwilioConfig([
            'from' => '+31612345678'
        ]);
        $twilio = new Twilio($this->twilioService, $config);
        $channel = new TwilioChannel($twilio, $this->events);

        $this->callWillBeSentToTwilioWith('+31612345678', '+22222222222', 'http://example.com');

        $channel->send(new NotifiableWithAttribute(), $this->notification);
    }

    protected function smsMessageWillBeSentToTwilioWith(...$args)
    {
        $this->twilioService->account->messages->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with(...$args)
            ->andReturn(true);
    }

    protected function callWillBeSentToTwilioWith(...$args)
    {
        $this->twilioService->account->calls->shouldReceive('create')
            ->atLeast()->once()
            ->with(...$args)
            ->andReturn(true);
    }
}
