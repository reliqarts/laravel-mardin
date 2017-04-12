<?php

namespace ReliQArts\Mardin\Transformers;

use Carbon\Carbon;
use ReliQArts\Mardin\Contracts\Thread;
use League\Fractal\TransformerAbstract;
use ReliQArts\Mardin\Helpers\StringHelper;
use ReliQArts\Mardin\Contracts\UserTransformer;

class ThreadTransformer extends TransformerAbstract
{
    /**
     * List of resources available to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'messages',
        'unreadMessages',
    ];

    /**
     * List of resources to automatically include.
     *
     * @var array
     */
    protected $defaultIncludes = [
        'latestMessage',
    ];

    /**
     * Transform the data.
     * @return array API suitable information.
     */
    public function transform(Thread $thread)
    {
        $user = auth()->user();
        $userId = $user ? $user->id : 0;
        $unread = $thread->isUnread($userId);
        $unreadCount = $thread->userUnreadMessagesCount($userId);
        $participants = $thread->participantsString($user);

        return [
            'id' => (int) $thread->id,
            'subject' => $thread->subject,
            'url' => route('show-message', ['id' => $thread->id]),
            'new' => $unreadCount,
            'unread' => $unread,
            'unread_count' => $unreadCount,
            'participants' => $participants,
            'participant_count' => $thread->participants()->count(),
            'deleted' => $thread->deleted_at,
            'created_at' => StringHelper::date(Carbon::createFromFormat('Y-n-j G:i:s', $thread->created_at)),
            'created_at_raw' => $thread->created_at,
            'updated_at' => $thread->updated_at ? StringHelper::date(Carbon::createFromFormat('Y-n-j G:i:s', $thread->updated_at)) : 'N/A',
            'updated_at_raw' => $thread->updated_at,
        ];
    }

    /**
     * Include Messages.
     *
     * @param Thread $thread
     * @return \League\Fractal\Resource\Collection
     */
    public function includeMessages(Thread $thread)
    {
        if ($messages = $thread->messages) {
            return $this->collection($messages, new MessageTransformer);
        }
    }

    /**
     * Include unread Messages.
     *
     * @param Thread $thread
     * @return \League\Fractal\Resource\Collection
     */
    public function includeUnreadMessages(Thread $thread)
    {
        if ($messages = $thread->userUnreadMessages(auth()->user()->id)) {
            return $this->collection($messages, new MessageTransformer);
        }
    }

    /**
     * Include last Message.
     *
     * @param Thread $thread
     * @return \League\Fractal\Resource\Item
     */
    public function includeLatestMessage(Thread $thread)
    {
        if ($message = $thread->latestMessage) {
            return $this->item($message, new MessageTransformer);
        }
    }

    /**
     * Include Participants.
     *
     * @param Thread $thread
     * @return \League\Fractal\Resource\Collection
     */
    public function includeParticipants(Thread $thread)
    {
        if ($participants = $thread->users) {
            return $this->collection($participants, new UserTransformer);
        }
    }
}
