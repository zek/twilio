<?php

namespace NotificationChannels\Twilio\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioCallMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use PHPUnit_Framework_TestCase;
use NotificationChannels\Twilio\Twilio;
use Services_Twilio_Rest_Calls;
use Services_Twilio_Rest_Messages;
use Services_Twilio as TwilioService;

class TwilioTest extends PHPUnit_Framework_TestCase
{
    /** @var Twilio */
    protected $twilio;

    /** @var TwilioService */
    protected $twilioService;

    /** @var Dispatcher */
    protected $dispatcher;

    public function setUp()
    {
        parent::setUp();

        $this->twilioService = Mockery::mock(TwilioService::class);
        $this->dispatcher = Mockery::mock(Dispatcher::class);

        $this->twilioService->account = new \stdClass();
        $this->twilioService->account->messages = Mockery::mock(Services_Twilio_Rest_Messages::class);
        $this->twilioService->account->calls = Mockery::mock(Services_Twilio_Rest_Calls::class);

        $this->twilio = new Twilio($this->twilioService, '+1234567890');
    }

    /** @test */
    public function it_can_send_a_sms_message_to_twilio()
    {
        $message = new TwilioSmsMessage('Message text');

        $this->twilioService->account->messages->shouldReceive('sendMessage')
            ->atLeast()->once()
            ->with('+1234567890', '+1111111111', 'Message text')
            ->andReturn(true);

        $this->twilio->sendMessage($message, '+1111111111');
    }

    /** @test */
    public function it_can_send_a_call_to_twilio()
    {
        $message = new TwilioCallMessage('http://example.com');
        $message->from = '+2222222222';

        $this->twilioService->account->calls->shouldReceive('create')
            ->atLeast()->once()
            ->with('+2222222222', '+1111111111', 'http://example.com')
            ->andReturn(true);

        $this->twilio->sendMessage($message, '+1111111111');
    }

    /** @test */
    public function it_will_throw_an_exception_in_case_of_a_missing_from_number()
    {
        $this->setExpectedException(
            CouldNotSendNotification::class,
            'Notification was not sent. Missing `from` number.'
        );

        $smsMessage = new TwilioSmsMessage('Message text');
        $twilio = new Twilio($this->twilioService, null);

        $twilio->sendMessage($smsMessage, null);
    }

    /** @test */
    public function it_will_throw_an_exception_in_case_of_an_unrecognized_message_object()
    {
        $this->setExpectedException(
            CouldNotSendNotification::class,
            'Notification was not sent. Message object class'
        );

        $this->twilio->sendMessage(new InvalidMessage(), null);
    }
}

class InvalidMessage extends TwilioMessage
{
}
