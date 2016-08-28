<?php

namespace NotificationChannels\Twilio;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Events\Dispatcher;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Events\NotificationFailed;
use Services_Twilio;

class TwilioChannel
{
    /**
     * @var \Services_Twilio
     */
    protected $twilio;

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $events;

    /**
     * TwilioChannel constructor.
     *
     * @param Services_Twilio $twilio
     * @param Dispatcher $events
     */
    public function __construct(Services_Twilio $twilio, Dispatcher $events)
    {
        $this->twilio = $twilio;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return mixed
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('twilio')) {
            if (! $to = $notifiable->phone_number) {
                return;
            }
        }

        try {
            $message = $notification->toTwilio($notifiable);
            $params = [];
            $from = null;

            if (is_string($message)) {
                $message = new TwilioSmsMessage($message);
            }

            if (! $message instanceof TwilioAbstractMessage) {
                throw CouldNotSendNotification::invalidMessageObject($message);
            }

            if (method_exists($notifiable, 'canReceiveAlphanumericSender') &&
                $notifiable->canReceiveAlphanumericSender() &&
                $sender = config('services.twilio.alphanumeric_sender')) {
                $from = $sender;
            } else {
                $from = $message->from ?: config('services.twilio.from');
            }

            if (! $from) {
                throw CouldNotSendNotification::missingFrom();
            }

            if ($serviceSid = config('services.twilio.sms_service_sid')) {
                $params['MessagingServiceSid'] = $serviceSid;
            }

            return $this->sendMessage($message, $from, $to, $params);
        } catch (Exception $exception) {
            $this->events->fire(
                new NotificationFailed($notifiable, $notification, 'twilio', ['message' => $exception->getMessage()])
            );
        }
    }

    /**
     * @param $message
     * @param $from
     * @param $to
     * @return mixed
     *
     * @throws \NotificationChannels\Twilio\Exceptions\CouldNotSendNotification
     */
    protected function sendMessage($message, $from, $to, $params = [])
    {
        if ($message instanceof TwilioSmsMessage) {
            return $this->twilio->account->messages->sendMessage(
                $from,
                $to,
                trim($message->content),
                null,
                $params
            );
        }

        if ($message instanceof TwilioCallMessage) {
            return $this->twilio->account->calls->create(
                $from,
                $to,
                trim($message->content)
            );
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }
}
