<?php

namespace NotificationChannels\Twilio;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Events\NotificationFailed;

class TwilioChannel
{
    /**
     * @var Twilio
     */
    protected $twilio;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * TwilioChannel constructor.
     *
     * @param Twilio  $twilio
     * @param Dispatcher  $events
     */
    public function __construct(Twilio $twilio, Dispatcher $events)
    {
        $this->twilio = $twilio;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $to = $this->getTo($notifiable);
            $message = $notification->toTwilio($notifiable);

            if (is_string($message)) {
                $message = new TwilioSmsMessage($message);
            }

            if (! $message instanceof TwilioMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            return $this->twilio->sendMessage($message, $to);
        } catch (Exception $exception) {
            $this->events->fire(
                new NotificationFailed($notifiable, $notification, 'twilio', ['message' => $exception->getMessage()])
            );
        }
    }

    protected function getTo($notifiable)
    {
        if ($notifiable->routeNotificationFor('twilio')) {
            return $notifiable->routeNotificationFor('twilio');
        }
        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}
