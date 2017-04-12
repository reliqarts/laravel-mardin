<?php

namespace ReliQArts\Mardin\Policies;

use ReliQArts\Mardin\Contracts\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if a User can send a message.
     *
     * @param  User $user
     * @return bool
     */
    public function send(User $user)
    {
        return $user->canSendMardinMessage();
    }

    /**
     * Determine if a User can receive a message.
     *
     * @param  User  $user
     * @return bool
     */
    public function receive(User $user)
    {
        return $user->canReceiveMardinMessage();
    }
}
