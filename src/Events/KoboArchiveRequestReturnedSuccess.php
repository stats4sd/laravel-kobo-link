<?php

namespace Stats4sd\KoboLink\Events;

;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Models\TeamXlsform;

class KoboArchiveRequestReturnedSuccess implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param $user
     * @param TeamXlsform $form
     * @return void
     */
    public function __construct(public TeamXlsform $form, public $user)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): Channel
    {
        $channel = $this->user?->id ?? 'admin';

        return new PrivateChannel("App.Models.User.{$channel}");
    }
}
