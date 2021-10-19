<?php

namespace Stats4sd\KoboLink\Events;

;

use Stats4sd\KoboLink\Models\TeamXlsform;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KoboUploadReturnedSuccess implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param TeamXlsform $form
     * @param mixed $user
     */
    public function __construct(public TeamXlsform $form, public mixed $user = null)
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
