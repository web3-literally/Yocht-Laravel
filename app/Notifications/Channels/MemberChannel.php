<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

/**
 * Class MemberChannel
 * @package App\Notifications\Channels
 */
class MemberChannel
{
    /**
     * @param $notifiable
     * @param Notification $notification
     * @return mixed
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification->id,
            'type' => get_class($notification),
            'instance_id' => $data['instance_id'] ?? null,
            'data' => $data,
            'read_at' => null,
        ]);
    }

}