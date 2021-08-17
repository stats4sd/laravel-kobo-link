<?php

namespace Stats4sd\KoboLink\Events;

;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Models\TeamXlsform;

class KoboDeploymentReturnedError implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public $form;
    public $errorType;
    public $errorMessage;

    /**
     * Create a new event instance.
     * @param $user
     * @param TeamXlsform $form
     * @param String $errorType
     * @param String $errorMessage
     * @return void
     */
    public function __construct($user = null, TeamXlsform $form, $errorType, $errorMessage)
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
    public function broadcastOn()
    {
        if ($this->user) {
            return new PrivateChannel("App.Models.User.{$this->user->id}");
        }
    }
}
