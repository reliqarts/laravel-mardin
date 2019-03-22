<?php

namespace ReliQArts\Mardin\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use ReliQArts\Mardin\Contracts\Message;
use ReliQArts\Mardin\Contracts\Thread;
use ReliQArts\Mardin\Contracts\UserTransformer;
use ReliQArts\Mardin\Helpers\StringHelper;

class MessageTransformer extends TransformerAbstract
{
    /**
     * List of resources available to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'thread',
    ];

    /**
     * List of resources to automatically include.
     *
     * @var array
     */
    protected $defaultIncludes = [
        'sender',
    ];

    /**
     * Transform the data.
     *
     * @return array API suitable information
     */
    public function transform(Message $message)
    {
        return [
            'id' => (int) $message->id,
            'thread_id' => $message->thread_id,
            'thread_url' => route('show-message', ['id' => $message->thread->id]),
            'sender_id' => $message->user_id,
            'sender_name' => $message->user->name,
            'body' => $message->body,
            'deleted' => $message->deleted_at,
            'created_at' => StringHelper::date(Carbon::createFromFormat('Y-n-j G:i:s', $message->created_at)),
            'created_at_raw' => $message->created_at,
            'updated_at' => $message->updated_at
                ? StringHelper::date(Carbon::createFromFormat('Y-n-j G:i:s', $message->updated_at)) : 'N/A',
            'updated_at_raw' => $message->updated_at,
        ];
    }

    /**
     * Include Sender.
     *
     * @param Message $message
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeSender(Message $message)
    {
        $userTransformer = app()->make(UserTransformer::class);
        if ($user = $message->user) {
            return $this->item($user, $userTransformer);
        }
    }

    /**
     * Include Thread.
     *
     * @param Message $message
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeThread(Message $message)
    {
        if ($thread = $message->thread) {
            return $this->item($thread, new ThreadTransformer());
        }
    }
}
