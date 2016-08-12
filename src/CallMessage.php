<?php

namespace NotificationChannels\Twilio;

class CallMessage
{
    /**
     * The message TwiML url.
     *
     * @var string
     */
    public $url;

    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from;

    /**
     * Create a new message instance.
     *
     * @param  string  $url
     * @return void
     */
    public function __construct($url = '')
    {
        $this->url = $url;
    }

    /**
     * Set the message content.
     *
     * @param  string  $url
     * @return $this
     */
    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the phone number the message should be sent from.
     *
     * @param  string  $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }
}