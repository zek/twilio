<?php

namespace NotificationChannels\Twilio;

class TwilioCallMessage extends TwilioAbstractMessage
{
    /**
     * Set the message url.
     *
     * @param  string  $url
     *
     * @return $this
     */
    public function url($url)
    {
        $this->content = $url;

        return $this;
    }
}
