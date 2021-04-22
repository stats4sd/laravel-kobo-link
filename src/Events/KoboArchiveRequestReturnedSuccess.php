<?php

namespace Stats4sd\KoboLink\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stats4sd\KoboLink\Models\Xlsform;

class KoboArchiveRequestReturnedSuccess implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $user;
    public Xlsform $form;

    /**
     * Create a new event instance.
     * @param User $user
     * @param Xlsform $form
     * @return void
     */
    public function __construct(User $user, Xlsform $form)
    {
        $this->user = $user;
        $this->form = $form;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): Channel | array
    {
        return new PrivateChannel("App.Models.User.{$this->user->id}");
    }
}
