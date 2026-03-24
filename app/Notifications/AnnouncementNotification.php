<?php

namespace App\Notifications;

use App\Models\Announcement;
use App\Models\PushSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** Reintentos si falla el envío */
    public int $tries = 3;
    /** Tiempo de espera entre reintentos (segundos) */
    public int $backoff = 10;

    public function __construct(public Announcement $announcement) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'body'  => $this->announcement->body,
            'url'   => '/announcements',
        ];
    }
}
