<?php

namespace Stats4sd\KoboLink\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Models\TeamXlsform;

class KoboUploadReturnedError implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public TeamXlsform $form;
    public string $errorType;
    public string $errorMessage;

    /**
     * Create a new event instance
     *
     * @param $user
     * @param TeamXlsform $form
     * @param String $errorType
     * @param String $errorMessage
     */
    public function __construct(TeamXlsform $form, string $errorType, string $errorMessage, $user = null)
    {
        $this->user = $user;
        $this->form = $form;
        $this->errorType = $errorType;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): Channel
    {
        if ($this->user) {
            return new PrivateChannel("App.Models.User.{$this->user->id}");
        }
    }
}
