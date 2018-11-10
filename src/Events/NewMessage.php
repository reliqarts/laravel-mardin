<?php

namespace ReliQArts\Mardin\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use ReliQArts\Mardin\Contracts\Message;
use ReliQArts\Mardin\Transformers\MessageTransformer;

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
     * @var Message message
     */
    public $message;

    /**
     * @var array channels for message
     */
    private $channels;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->channels = [];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'newMessage';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array|Channel
     */
    public function broadcastOn()
    {
        // Get channels for Broadcast
        foreach ($this->message->thread->participantsUserIds() as $recipient) {
            if ($recipient === $this->message->user->id) {
                continue;
            }
            $this->channels[] = new PrivateChannel("Mardin.Messages.User.{$recipient}");
        }

        // Set Message as Fractal Item
        $fractal = new Manager();
        $fractal->parseIncludes('thread');
        $this->message = $fractal->createData(new Item($this->message, new MessageTransformer()))->toArray();

        return $this->channels;
    }
}
