<?php

namespace NotificationChannels\Twilio;

use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\Events\MessageWasSent;
use NotificationChannels\Twilio\Events\SendingMessage;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Services_Twilio;

class TwilioChannel
{
    /**
     * @var \Services_Twilio
     */
    protected $twilio;

    public function __construct(Services_Twilio $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$to = $notifiable->routeNotificationFor('twilio')) {
            return;
        }

        $message = $notification->toTwilio($notifiable);

        if (is_string($message)) {
            // Default to SMS message if only a string is provided
            $message = new TwilioSmsMessage($message);
        }

        if (!$message instanceof TwilioSmsMessage::class &&
            !$message instanceof TwilioCallMessage::class
        ) {
            $class = get_class($message) ?: 'Unknown';

            throw CouldNotSendNotification::invalidMessageObject($class);
        }

        if (!$from = $message->from ?: config('services.twilio.from')) {
            throw CouldNotSendNotification::missingFrom();
        }

        $shouldSendMessage = event(new SendingMessage($notifiable, $notification, $message), [], true) !== false;

        if (!$shouldSendMessage) {
            return;
        }

        $response = null;

        /** @var TwilioSmsMessage|TwilioCallMessage $message */
        try {
            if ($message instanceof TwilioSmsMessage::class) {
                $response = $this->twilio->account->messages->sendMessage(
                    $from,
                    $to,
                    trim($message->content)
                );
            } elseif ($message instanceof TwilioCallMessage::class) {
                $response = $this->twilio->account->calls->create(
                    $from,
                    $to,
                    trim($message->url)
                );
            }
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnException($exception);
        }

        event(new MessageWasSent($notifiable, $notification, $response));
    }
}
