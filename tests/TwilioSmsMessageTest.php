<?php

namespace NotificationChannels\Twilio\Test;

use NotificationChannels\Twilio\TwilioSmsMessage;
use PHPUnit_Framework_TestCase;

class TwilioSmsMessageTest extends PHPUnit_Framework_TestCase
{
    /** @var \NotificationChannels\Twilio\TwilioSmsMessage */
    protected $message;

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
    public function it_can_set_the_content()
    {
        $this->message->content('myMessage');

        $this->assertEquals('myMessage', $this->message->content);
    }

    /** @test */
    public function it_can_set_the_from()
    {
        $this->message->from('+1234567890');

        $this->assertEquals('+1234567890', $this->message->from);
    }
}
