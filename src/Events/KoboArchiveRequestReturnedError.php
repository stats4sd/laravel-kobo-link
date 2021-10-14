<?php

namespace Stats4sd\KoboLink\Events;

;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\TeamXlsform;

class KoboArchiveRequestReturnedError implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @return void
     */
    public function __construct(public TeamXlsform $form,
                                public string      $errorType,
                                public string      $errorMessage,
                                public mixed       $user = null)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        $channel = $this->user?->id ?? 'admin';
        return new PrivateChannel("App.Models.User.{$channel}");
    }
}
