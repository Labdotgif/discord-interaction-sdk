<?php

namespace Labdotgif\DiscordInteraction\Event\Interaction;

use Labdotgif\DiscordInteraction\Message\Component\DiscordComponentTypeEnum;

class MessageComponentInteractionDiscordEvent extends DiscordInteractionEvent
{
    public const EVENT_NAME = 'interaction.message_component';

    private readonly DiscordComponentTypeEnum $componentType;
    private readonly string $customId;

    public function __construct(DiscordComponentTypeEnum $componentType, string $customId, array $rawData)
    {
        parent::__construct($rawData);

        $this->componentType = $componentType;
        $this->customId = $customId;
    }

    public function getComponentType(): DiscordComponentTypeEnum
    {
        return $this->componentType;
    }

    public function getCustomId(): string
    {
        return $this->customId;
    }

    public static function getEventName(): string
    {
        return static::EVENT_NAME;
    }
}
