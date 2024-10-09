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
    private \LINE\Clients\MessagingApi\Api\MessagingApiApi $api;

    public function __construct(
        #[\SensitiveParameter] private readonly \LINE\Clients\MessagingApi\Configuration $config,
        private readonly string                                                          $receiver,
        ?HttpClientInterface                                                             $client = null,
        ?EventDispatcherInterface                                                        $dispatcher = null,
    )
    {
        parent::__construct($client, $dispatcher);

        $this->api = new \LINE\Clients\MessagingApi\Api\MessagingApiApi(
            client: $client,
            config: $this->config
        );
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof ChatMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, ChatMessage::class, $message);
        }

        $pushMessageRequest = (new PushMessageRequest())
            ->setTo($this->receiver)
            ->setMessages([
                new Message([
                    'type' => 'text',
                    'text' => $message->getSubject(),
                ]),
            ]);

        try {
            $this->api->pushMessage($pushMessageRequest);
        } catch (\LINE\Clients\MessagingApi\ApiException $e) {
            throw new RuntimeException(
                "Unable to send messages to LINE: \"{$e->getMessage()}\"",
                $e->getCode(),
                previous: $e,
            );
        } catch (\InvalidArgumentException $e) {
            throw new MalformedMessageRequestException($pushMessageRequest, previous: $e);
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
