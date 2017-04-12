<?php

namespace ReliQArts\Mardin\Contracts;

/**
 * A true mardin user defines.
 */
interface User
{
    /**
     * Whether a user can send a mardin message.
     * 
     * @return bool
     */
    public function canSendMardinMessage();

    /**
     * Whether a user can receive a mardin message.
     * 
     * @return bool
     */
    public function canReceiveMardinMessage();
}