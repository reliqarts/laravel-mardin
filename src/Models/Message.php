<?php

namespace ReliQArts\Mardin\Models;

use Cmgmyr\Messenger\Models\Message as MessengerMessage;
use ReliQArts\Mardin\Contracts\Message as MessageContract;

class Message extends MessengerMessage implements MessageContract
{
}
