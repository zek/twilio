<?php

namespace NotificationChannels\Twilio\Events;

use Illuminate\Notifications\Notification;

class MessageWasSent
{
    protected $notifiable;

    /** @var \Illuminate\Notifications\Notification */
    protected $notification;

    protected $response;

    /**
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @param mixed|null $response
     */
    public function __construct($notifiable, Notification $notification, $response = null)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
        $this->response = $response;
    }
}
