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
     * @var TwilioConfig
     */
    private $config;

    /**
     * Twilio constructor.
     *
     * @param  TwilioService $twilioService
     * @param TwilioConfig   $config
     */
    public function __construct(TwilioService $twilioService, TwilioConfig $config)
    {
        $this->twilioService = $twilioService;
        $this->config = $config;
    }

    /**
     * Send a TwilioMessage to the a phone number.
     *
     * @param  TwilioMessage $message
     * @param                $to
     * @param bool           $useAlphanumericSender
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function sendMessage(TwilioMessage $message, $to, $useAlphanumericSender = false)
    {
        if ($message instanceof TwilioSmsMessage) {
            if ($useAlphanumericSender && $sender = $this->getAlphanumericSender()) {
                $message->from($sender);
            }

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
            trim($message->content),
            null,
            $this->config->getSmsParams()
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
        if (! $from = $message->getFrom() ?: $this->config->getFrom()) {
            throw CouldNotSendNotification::missingFrom();
        }

        return $from;
    }

    protected function getAlphanumericSender()
    {
        if ($sender = $this->config->getAlphanumericSender()) {
            return $sender;
        }

        return null;
    }
}
