<?php

namespace Labdotgif\DiscordInteraction\Serializer\Normalizer;

use Discord\InteractionType;
use Labdotgif\DiscordInteraction\Event\DiscordEventInterface;
use Labdotgif\DiscordInteraction\Event\Interaction\AckPingInteractionDiscordEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InteractionAckPingDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        return new AckPingInteractionDiscordEvent($data);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return
            DiscordEventInterface::class === $type
            && 'json' === $format
            && isset($data['type'])
            && InteractionType::PING === $data['type']
            && isset($data['token']);
    }
}
