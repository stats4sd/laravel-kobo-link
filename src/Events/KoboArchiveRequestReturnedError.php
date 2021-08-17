<?php

namespace Stats4sd\KoboLink\Events;

;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stats4SD\KoboLink\Models\TeamXlsform;

class KoboArchiveRequestReturnedError implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public TeamXlsform $form;
    public string $errorType;
    public string $errorMessage;

    /**
     * Create a new event instance.
     * @return void
     */
    public function __construct($user = null, TeamXlsform $form, string $errorType, string $errorMessage)
    {
        //
        $this->user = $user;
        $this->form = $form;
        $this->errorType = $errorType;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        if ($this->user) {
            return new PrivateChannel("App.Models.User.{$this->user->id}");
        }
    }
}
