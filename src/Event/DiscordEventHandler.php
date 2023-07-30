<?php

namespace Labdotgif\DiscordInteraction\Event;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class DiscordEventHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SerializerInterface $serializer;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $eventDispatcher)
    {
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;

        $this->setLogger(new NullLogger());
    }

    public function handle(string $content, Request $request): ?Response
    {
        try {
            /** @var DiscordEventInterface $event */
            $event = $this->serializer->deserialize($content, DiscordEventInterface::class, 'json');
        } catch (NotNormalizableValueException $e) {
            $this->logger->debug('Cannot deserialize content', [
                'content' => $content,
                'exception' => $e
            ]);

            return null;
        }

        $this->logger->debug(sprintf('Dispatching Discord event "%s"', $event::getEventName()), [
            'payload' => json_decode($content, true)
        ]);

        $event->setRequest($request);
        $this->eventDispatcher->dispatch($event, $event::getEventName());

        if ($event instanceof ResponseAwareDiscordEvent) {
            return $event->getResponse();
        }

        return null;
    }
}
