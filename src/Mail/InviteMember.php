<?php

namespace Stats4sd\KoboLink\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Stats4sd\KoboLink\Models\Invite;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteMember extends Mailable
{
    use Queueable, SerializesModels;

    public $invite;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
        ->subject(config('app.name'). ': Invitation To Join Team ' . $this->invite->team->name)
        ->markdown('emails.invite');
    }
}
