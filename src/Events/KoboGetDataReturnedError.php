<?php

namespace Stats4sd\KoboLink\Events;

use App\Models\User;
use Illuminate\Support\Facades\Response;
use Stats4sd\KoboLink\Models\Xlsform;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class KoboGetDataReturnedError implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Xlsform $form;
    public Response $response;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Xlsform $form, Response $response)
    {
        $this->user = $user;
        $this->form = $form;
        $this->response = $response;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): Channel|array
    {
        return new PrivateChannel("App.Models.User.{$this->user->id}");
    }
}
