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

class KoboDeploymentReturnedError implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param $user
     * @param TeamXlsform $form
     * @param String $errorType
     * @param String $errorMessage
     * @return void
     */
    public function __construct(public TeamXlsform $form, public string $errorType, public string $errorMessage, public $user = null)
    {
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        $channel = $this->user?->id ?? 'admin';

        return new PrivateChannel("App.Models.User.{$channel}");
    }
}
