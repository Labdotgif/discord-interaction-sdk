<?php

namespace Labdotgif\DiscordInteraction\Serializer\Normalizer;

use Discord\InteractionType;
use Labdotgif\DiscordInteraction\Event\DiscordEventInterface;
use Labdotgif\DiscordInteraction\Event\Interaction\SlashCommandInteractionDiscordEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InteractionSlashCommandDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): SlashCommandInteractionDiscordEvent
    {
        return new SlashCommandInteractionDiscordEvent($data['data']['name'], $data);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return
            DiscordEventInterface::class === $type
            && 'json' === $format
            && isset($data['type'])
            && InteractionType::APPLICATION_COMMAND === $data['type'];
    }
}
