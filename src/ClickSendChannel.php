<?php

namespace JordanHavard\ClickSend;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

class ClickSendChannel
{
    /** @var ClickSendApi */
    protected $client;

    /** @var Dispatcher */
    protected $events;

    public function __construct(ClickSendApi $client, Dispatcher $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    /**
     * @return array|mixed
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationForClicksend();

        $message = $notification->toClickSend($notifiable);

        // always return object
        if (is_string($message)) {
        $message = new ClickSendMessage($message);
        }

        // array [success, message, data]
        $result = $this->client->sendSms($message->from, $to, $message->content, $message->custom, $message->delay);

        if (empty($result['success'])) {

            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, get_class($this), $result)
            );

        }

        return $result;
    }
}
