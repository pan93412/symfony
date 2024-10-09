<?php

namespace Symfony\Component\Notifier\Bridge\LineBot\Exception;

use LINE\Clients\MessagingApi\Model\PushMessageRequest;

class MalformedMessageRequestException extends \Symfony\Component\Notifier\Exception\InvalidArgumentException
{
    public function __construct(
        public readonly PushMessageRequest $request,
        \Throwable $previous = null
    )
    {
        $message = "This message request is malformed: {$request}";

        parent::__construct($message, previous: $previous);
    }
}
