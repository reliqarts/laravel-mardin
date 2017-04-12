<?php

namespace ReliQArts\Mardin\Events;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use ReliQArts\Mardin\Contracts\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use ReliQArts\Mardin\Transformers\MessageTransformer;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessage implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * The name of the queue on which to place the event.
     *
     * @var string
     */
    public $broadcastQueue = 'mardin';

    /**
     * @var array Channels for message.
     */
    private $channels;

    /**
     * @var Message Message.
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->channels = [];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        // Get channels for Broadcast
        foreach ($this->message->thread->participantsUserIds() as $recipient) {
            if ($recipient == $this->message->user->id) {
                continue;
            }
            $this->channels[] = new PrivateChannel("App.User.{$recipient}.Messages");
        }

        // Set Message as Fractal Item
        $fractal = new Manager;
        $fractal->parseIncludes('thread');
        $this->message = $fractal->createData(new Item($this->message, new MessageTransformer))->toArray();

        return $this->channels;
    }
}
