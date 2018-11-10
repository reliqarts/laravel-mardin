<?php

namespace ReliQArts\Mardin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use ReliQArts\Mardin\Contracts\User;

class Message
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    /**
     * Determine if a User can send a message.
     *
     * @param User $user
     *
     * @return bool
     */
    public function send(User $user)
    {
        return $user->canSendMardinMessage();
    }

    /**
     * Determine if a User can receive a message.
     *
     * @param User $user
     *
     * @return bool
     */
    public function receive(User $user)
    {
        return $user->canReceiveMardinMessage();
    }
}
