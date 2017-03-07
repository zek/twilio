<?php

namespace NotificationChannels\Twilio\Test;

use NotificationChannels\Twilio\TwilioSmsMessage;

class TwilioSmsMessageTest extends TwilioMessageTest
{
    public function setUp()
    {
        parent::setUp();

        $this->message = new TwilioSmsMessage();
    }

    /** @test */
    public function it_can_accept_a_message_when_constructing_a_message()
    {
        $message = new TwilioSmsMessage('myMessage');

        $this->assertEquals('myMessage', $message->content);
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $message = TwilioSmsMessage::create('myMessage');

        $this->assertEquals('myMessage', $message->content);
    }

    /** @test */
    public function it_sets_alphanumeric_sender()
    {
        $message = TwilioSmsMessage::create('myMessage');
        $message->sender('TestSender');

        $this->assertEquals('TestSender', $message->alphaNumSender);
    }

    /** @test */
    public function it_can_return_the_alphanumeric_sender_if_set()
    {
        $message = TwilioSmsMessage::create('myMessage');
        $message->sender('TestSender');

        $this->assertEquals('TestSender', $message->getFrom());
    }
}
