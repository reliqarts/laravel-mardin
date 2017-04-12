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
     * @return array API suitable user information.
     */
    public function transform(User $user);
}
