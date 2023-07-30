<?php

namespace Labdotgif\DiscordInteraction\Serializer\Normalizer;

use Discord\InteractionType;
use Labdotgif\DiscordInteraction\Event\DiscordEventInterface;
use Labdotgif\DiscordInteraction\Event\Interaction\MessageComponentInteractionDiscordEvent;
use Labdotgif\DiscordInteraction\Message\Component\DiscordComponentTypeEnum;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class InteractionMessageComponentDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, string $format = null, array $context = []): MessageComponentInteractionDiscordEvent
    {
        return new MessageComponentInteractionDiscordEvent(
            DiscordComponentTypeEnum::from($data['data']['component_type']),
            $data['data']['custom_id'],
            $data
        );
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return
            DiscordEventInterface::class === $type
            && 'json' === $format
            && isset($data['type'])
            && !empty($data['data']['custom_id'])
            && InteractionType::MESSAGE_COMPONENT === $data['type'];
    }
}
