<?php

namespace ReliQArts\Mardin\Contracts;

/**
 * A true mardin user transformer defines.
 */
interface UserTransformer
{
    /**
     * Transform mardin user.
     *
     * @param User $user Mardin user.
     */
    public function transform(User $user);
}
