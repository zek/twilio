<?php

namespace NotificationChannels\Twilio;

use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Services_Twilio as TwilioService;

class Twilio
{
    /**
     * @var TwilioService
     */
    protected $twilioService;

    /**
     * Default 'from' from config.
     * @var string
     */
    protected $from;

    /**
     * Twilio constructor.
     *
     * @param  TwilioService  $twilioService
     * @param  string  $from
     */
    public function __construct(TwilioService $twilioService, $from)
    {
        $this->twilioService = $twilioService;
        $this->from = $from;
    }

    /**
     * Send a TwilioMessage to the a phone number.
     *
     * @param  TwilioMessage  $message
     * @param  $to
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function sendMessage(TwilioMessage $message, $to)
    {
        if ($message instanceof TwilioSmsMessage) {
            return $this->sendSmsMessage($message, $to);
        }

        if ($message instanceof TwilioCallMessage) {
            return $this->makeCall($message, $to);
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    protected function sendSmsMessage($message, $to)
    {
        return $this->twilioService->account->messages->sendMessage(
            $this->getFrom($message),
            $to,
            trim($message->content)
        );
    }

    protected function makeCall($message, $to)
    {
        return $this->twilioService->account->calls->create(
            $this->getFrom($message),
            $to,
            trim($message->content)
        );
    }

    protected function getFrom($message)
    {
        if (! $from = $message->from ?: $this->from) {
            throw CouldNotSendNotification::missingFrom();
        }

        return $from;
    }
}
