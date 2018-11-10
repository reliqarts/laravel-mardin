<?php

namespace ReliQArts\Mardin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal;
use ReliQArts\Mardin\Contracts\Message;
// use ReliQArts\Mardin\Events\NewMessage;
use ReliQArts\Mardin\Contracts\Participant;
use ReliQArts\Mardin\Contracts\Thread;
use ReliQArts\Mardin\Contracts\User;
use ReliQArts\Mardin\Events\NewMessage;
use ReliQArts\Mardin\Transformers\MessageTransformer;
use ReliQArts\Mardin\Transformers\ThreadTransformer;

class MessagesController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Thread model.
     *
     * @var Thread
     */
    protected $threads;

    /**
     * Message model.
     *
     * @var Message
     */
    protected $messages;

    /**
     * Participant model.
     *
     * @var Participant
     */
    protected $participants;

    /**
     * User model.
     *
     * @var User
     */
    protected $users;

    /**
     * Fractal manager instance.
     *
     * @var Fractal\Manager
     */
    protected $fractal;

    /**
     * Constructor.
     */
    public function __construct(Thread $threads, Message $messages, User $users, Participant $participants)
    {
        $this->threads = $threads;
        $this->messages = $messages;
        $this->users = $users;
        $this->participants = $participants;
        $this->fractal = new Fractal\Manager();
    }

    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        $this->authorize('receive', resolve(Message::class));

        $currentUserId = auth()->user()->id;
        $title = 'Inbox';

        // All threads that user is participating in
        $threads = $this->threads->forUser($currentUserId)->get();

        return view(config('mardin.views.wrappers.index'), compact('threads', 'currentUserId', 'title'));
    }

    /**
     * Shows a message thread.
     *
     * @param Thread $thread
     *
     * @return mixed
     */
    public function show(Thread $thread)
    {
        $this->authorize('receive', resolve(Message::class));

        // don't show the current user in list
        $userId = auth()->user()->id;
        $otherUsersIds = array_except($thread->participantsUserIds(), [$userId]);
        $users = $this->users->whereIn('id', $otherUsersIds)->get();
        $title = "{$thread->subject} &mdash; Inbox";

        if (!$users->count()) {
            $errorMessage = 'Could not load thread. Participants error.';

            return redirect()->back()->with([
                'message' => $errorMessage,
                'error' => $errorMessage,
                'status' => $errorMessage,
            ]);
        }

        $thread->markAsRead($userId);
        $thread->deleted_at = null;

        return view(config('mardin.views.wrappers.show'), compact('thread', 'users', 'title'));
    }

    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->authorize('send', resolve(Message::class));

        $users = $this->users->where('id', '!=', auth()->id())->get();
        $title = $request->subject ?: 'New Conversation';
        $subject = $request->subject ?: 'New Conversation';
        $recipients = $request->recipients ?: [];
        $infoLine = 'New Conversation';

        if (count($recipients)) {
            $recipientNames = $this->users->whereIn('id', $recipients)->get()->map(function ($r) {
                return $r->name;
            })->toArray();
            $recipientNames = implode(', ', $recipientNames);

            if ($request->isOffer) {
                $infoLine = "Offer to ${recipientNames}";
            } else {
                $infoLine = "Between ${recipientNames} and You";
            }
        }

        return view(config('mardin.views.wrappers.show'), compact('users', 'title', 'recipients', 'subject', 'infoLine'));
    }

    /**
     * Stores a new message thread.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $this->authorize('send', resolve(Message::class));

        $thread = $this->threads->create([
            'subject' => $request->subject,
        ]);

        // Message
        $message = $this->messages->create(
            [
                'thread_id' => $thread->id,
                'user_id' => auth()->user()->id,
                'body' => $request->message,
            ]
        );

        // Sender
        $this->participants->create([
            'thread_id' => $thread->id,
            'user_id' => auth()->user()->id,
            'last_read' => new Carbon(),
        ]);

        // Recipients
        if ($request->has('recipients')) {
            $thread->addParticipant($request->recipients);
        }

        event(new NewMessage($message));
        $resource = new Fractal\Resource\Item($message, new MessageTransformer());
        $this->fractal->parseIncludes('thread');

        return $this->fractal->createData($resource)->toArray();
    }

    /**
     * Adds a new message to a current thread.
     *
     * @param Illuminate\Http\Request $request
     * @param $id
     *
     * @return mixed
     */
    public function update(Request $request, Thread $thread)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $this->authorize('send', resolve(Message::class));

        $thread->activateAllParticipants();

        // Message
        $message = $this->messages->create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'body' => $request->message,
        ]);

        // Add replier as a participant
        $participant = $this->participants->firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => auth()->user()->id,
        ]);
        $participant->last_read = new Carbon();
        $participant->save();

        // Recipients
        if ($request->has('recipients')) {
            $thread->addParticipant($request->recipients);
        }

        event(new NewMessage($message));
        $resource = new Fractal\Resource\Item($message, new MessageTransformer());
        $this->fractal->parseIncludes('thread');

        return $this->fractal->createData($resource)->toArray();
    }

    /**
     * Mark a specific thread as read, for ajax use.
     *
     * @param $id
     */
    public function read(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        if ($threadIds = $request->threads) {
            $threads = $this->threads->whereIn('id', $threadIds)->get();
            foreach ($threads as $thread) {
                $thread->markAsRead(auth()->id());
            }
            $collection = new Fractal\Resource\Collection($threads, new ThreadTransformer());

            return $this->fractal->createData($collection)->toArray();
        }
    }

    /**
     * Mark a specific thread as unread, for ajax use.
     *
     * @param $id
     */
    public function unread(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        if ($threadIds = $request->threads) {
            $threads = $this->threads->whereIn('id', $threadIds)->get();
            foreach ($threads as $thread) {
                $thread->markAsUnread(auth()->id());
            }
            $collection = new Fractal\Resource\Collection($threads, new ThreadTransformer());

            return $this->fractal->createData($collection)->toArray();
        }
    }

    /**
     * Delete a thread, for ajax use.
     *
     * @param $id
     */
    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        if ($threadIds = $request->threads) {
            return $threads = $this->threads->whereIn('id', $threadIds)->delete();
        }
    }

    /**
     * Get the number of unread threads, for ajax use.
     *
     * @return array
     */
    public function unreadCount()
    {
        $count = auth()->user()->newMessagesCount();

        return ['msg_count' => $count];
    }

    /**
     * Get threads for user. (inbox).
     *
     * @param Illuminate\Http\Request $request
     * @param string                  $filter  filter messages for inbox
     */
    public function inboxData(Request $request, $filter = 'all')
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $userId = $request->user()->id;
        $threads = $this->threads;

        switch ($filter) {
            case 'new':
            case 'unread':
                $threads = $threads->forUserWithNewMessages($userId)->latest('updated_at')->get();

                break;
            default:
                $threads = $threads->forUser($userId)->latest('updated_at')->get();

                break;
        }

        $collection = new Fractal\Resource\Collection($threads, new ThreadTransformer());

        return $this->fractal->createData($collection)->toArray();
    }

    /**
     * Get messgges for thread. (inbox).
     *
     * @param Illuminate\Http\Request $request
     * @param string                  $thread  thread to get messages for
     */
    public function threadMessagesData(Request $request, Thread $thread)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $userId = $request->user()->id;
        $limit = $request->limit ?: 200;
        $page = $request->p ?: 1;

        $messages = $thread->messages()->skip($page - 1)->take($limit)->get();
        $thread = new Fractal\Resource\Item($thread, new ThreadTransformer());
        $messages = new Fractal\Resource\Collection($messages, new MessageTransformer());

        return [
            'messages' => $this->fractal->createData($messages)->toArray(),
            'thread' => $this->fractal->createData($thread)->toArray(),
        ];
    }
}
