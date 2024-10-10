<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\LineBot;

use LINE\Clients\MessagingApi\Model\Message;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use Symfony\Component\Notifier\Bridge\LineBot\Exception\MalformedMessageRequestException;
use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * * @author Yi-Jyun Pan <me@pan93.com>
 */
final class LineBotTransport extends AbstractTransport
{
    public function __construct(
        #[\SensitiveParameter] private readonly string $channelAccessToken,
        private readonly string                        $receiver,
        ?HttpClientInterface                           $client = null,
        ?EventDispatcherInterface                      $dispatcher = null,
    )
    {
        parent::__construct($client, $dispatcher);
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof ChatMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, ChatMessage::class, $message);
        }

        $response = $this->client->request(
            'POST',
            "https://{$this->getEndpoint()}/api/notify",
            [
                'auth_bearer' => $this->channelAccessToken,
                'json' => [
                    'to' => $this->receiver,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message->getSubject(),
                        ],
                    ],
                ],
            ],
        );

        try {
            $statusCode = $response->getStatusCode();
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            throw new TransportException('Could not reach the remote LINE server.', $response, 0, $e);
        }

        if (200 !== $statusCode) {
            $originalContent = $message->getSubject();

            $result = $response->toArray(false) ?: ['message' => ''];
            $errorMessage = trim($result['message']);

            throw new TransportException("Unable to post the LINE message: \"$originalContent\" ($statusCode: \"$errorMessage\")", $response);
        }

        return new SentMessage($message, (string)$this);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof ChatMessage;
    }

    public function __toString(): string
    {
        return \sprintf('linebot://%s?receiver=%s', $this->getEndpoint(), $this->receiver);
    }
}
