<?php

namespace ReliQArts\Mardin\Models;

use ModelNotFoundException;
use Cmgmyr\Messenger\Models\Thread as MessengerThread;
use ReliQArts\Mardin\Contracts\Thread as ThreadContract;

class Thread extends MessengerThread implements ThreadContract
{
    /**
     * {@inheritdoc}
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id', 'id')->orderBy('updated_at', 'desc');
    }

    /**
     * Generates a string of participant information.
     *
     * @param User  $excludingUser User to be excluded from string.
     * @param bool  $array Whether an array should be returned.
     *
     * @return string
     */
    public function participantsString($excludingUser = null, $array = false)
    {
        $participants = $this->users->map(function ($participant) use ($excludingUser) {
            if (! $excludingUser || $excludingUser->name != $participant->name) {
                return $participant->name;
            }
        })->reject(function ($name) {
            return empty($name);
        })->toArray();

        return $array ? $participants : implode(', ', $participants);
    }

    /**
     * Mark a thread as unread for a user.
     *
     * @param int $userId
     */
    public function markAsUnread($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            $participant->last_read = null;
            $participant->save();
        } catch (ModelNotFoundException $e) {
            // do nothing
        }
    }
}
