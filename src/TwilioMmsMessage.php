<?php

namespace NotificationChannels\Twilio;

class TwilioMmsMessage extends TwilioSmsMessage
{
    /**
     * The message media url (for MMS messages).
     *
     * @var string
     */
    public $mediaUrl;

    /**
     * Set the alphanumeric sender.
     *
     * @param $url
     */
    public function mediaUrl($url)
    {
        $this->mediaUrl = $url;
    }
}
