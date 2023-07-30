<?php

namespace Labdotgif\DiscordInteraction\Event\Subscriber\Interaction;

use Discord\Interaction;
use Discord\InteractionResponseType;
use Labdotgif\DiscordInteraction\Event\Interaction\AckPingInteractionDiscordEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AckPingDiscordIntegrationSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private readonly string $env;
    private readonly string $publicKey;

    public function __construct(string $env, string $publicKey)
    {
        $this->env = $env;
        $this->publicKey = $publicKey;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AckPingInteractionDiscordEvent::getEventName() => [
                ['onAckPing', 0]
            ]
        ];
    }

    public function onAckPing(AckPingInteractionDiscordEvent $event): void
    {
        $response = new JsonResponse();

        if (null === $event->getSignatureFingerprint() || null === $event->getSignatureTimestamp()) {
            $this->logger->debug('Unknown signature fingerprint or timestamp');

            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

            $event->setResponse($response);

            return;
        }

        try {
            if (Interaction::verifyKey(
                $event->getBody(),
                $event->getSignatureFingerprint(),
                $event->getSignatureTimestamp(),
                $this->publicKey
            )) {
                $response->setData([
                    'type' => InteractionResponseType::PONG
                ]);

                $this->logger->debug('Success ping ack');
            } else {
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

                $this->logger->debug('Failed ping ack');
            }
        } catch (\Exception $e) {
            if ('dev' === $this->env) {
                throw $e;
            }

            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        $event->setResponse($response);
    }
}
