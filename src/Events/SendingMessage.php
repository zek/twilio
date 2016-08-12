<?php

namespace NotificationChannels\Twilio\Events;

use Illuminate\Notifications\Notification;

class SendingMessage
{
    protected $notifiable;

    /** @var \Illuminate\Notifications\Notification */
    protected $notification;

    /** @var mixed|\NotificationChannels\Twilio\CallMessage|\NotificationChannels\Twilio\SmsMessage */
    protected $message;

    /**
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @param \NotificationChannels\Twilio\SmsMessage|\NotificationChannels\Twilio\CallMessage|mixed $message
     */
    public function __construct($notifiable, Notification $notification, $message)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
        $this->message = $message;
    }
}
