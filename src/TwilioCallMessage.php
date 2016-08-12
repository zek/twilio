<?php

namespace NotificationChannels\Twilio;

class TwilioCallMessage
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
     * @param string $url
     *
     * @return static
     */
    public static function create($url = '')
    {
        return new static($url);
    }

    /**
     * Create a new message instance.
     *
     * @param  string  $url
     */
    public function __construct($url = '')
    {
        $this->url = $url;
    }

    /**
     * Set the message content.
     *
     * @param  string  $url
     *
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
     *
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }
}
