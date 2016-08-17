<?php

namespace NotificationChannels\Twilio\Test;

use NotificationChannels\Twilio\TwilioCallMessage;
use PHPUnit_Framework_TestCase;

class TwilioCallMessageTest extends PHPUnit_Framework_TestCase
{
    /** @var \NotificationChannels\Twilio\TwilioCallMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();

        $this->message = new TwilioCallMessage();
    }

    /** @test */
    public function it_can_accept_an_url_when_constructing_a_message()
    {
        $message = new TwilioCallMessage('http://example.com');

        $this->assertEquals('http://example.com', $message->content);
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $message = TwilioCallMessage::create('http://example.com');

        $this->assertEquals('http://example.com', $message->content);
    }

    /** @test */
    public function it_can_set_the_url()
    {
        $this->message->url('http://example.com');

        $this->assertEquals('http://example.com', $this->message->content);
    }

    /** @test */
    public function it_can_set_the_from()
    {
        $this->message->from('+1234567890');

        $this->assertEquals('+1234567890', $this->message->from);
    }
}
