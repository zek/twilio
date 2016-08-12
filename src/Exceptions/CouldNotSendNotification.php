<?php

namespace NotificationChannels\Twilio\Exceptions;

use NotificationChannels\Twilio\CallMessage;
use NotificationChannels\Twilio\SmsMessage;

class CouldNotSendNotification extends \Exception
{
    /**
     * @param \Exception $exception
     *
     * @return static
     */
    public static function serviceRespondedWithAnException($exception)
    {
        return new static("Notification was not sent. Twilio responded with `{$exception->getCode()}: {$exception->getMessage()}`");
    }

    /**
     * @param string $class
     *
     * @return static
     */
    public static function invalidMessageObject($class)
    {
        return new static("Message object class `{$class}` is invalid. It should be either `".SmsMessage::class.'` or `'.CallMessage::class.'`');
    }
}
