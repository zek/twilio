<?php

namespace NotificationChannels\Twilio;

use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\Events\MessageWasSent;
use NotificationChannels\Twilio\Events\SendingMessage;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Services_Twilio;

class Channel
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

        $config = config('services.twilio');

        $message = $notification->toTwilio($notifiable);

        if (is_string($message)) {
            $message = new SmsMessage($message);
        }

        if (!$message instanceof SmsMessage::class &&
            !$message instanceof CallMessage::class
        ) {
            $class = get_class($message) ?: 'Unknown';

            throw CouldNotSendNotification::invalidMessageObject($class);
        }

        $shouldSendMessage = event(new SendingMessage($notifiable, $notification, $message), [], true) !== false;

        if (!$shouldSendMessage) {
            return;
        }

        $response = null;

        /** @var SmsMessage|CallMessage $message */
        try {
            if ($message instanceof SmsMessage::class) {
                $response = $this->twilio->account->messages->sendMessage(
                    $message->from ?: $config['from'],
                    $to,
                    trim($message->content)
                );
            } elseif ($message instanceof CallMessage::class) {
                $response = $this->twilio->account->calls->create(
                    $message->from ?: $config['from'],
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
