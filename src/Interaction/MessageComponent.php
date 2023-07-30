<?php

namespace Labdotgif\DiscordInteraction\Interaction;

use Labdotgif\DiscordInteraction\DiscordServiceSpool;
use Labdotgif\DiscordInteraction\Event\Interaction\MessageComponentInteractionDiscordEvent;
use Labdotgif\DiscordInteraction\Message\Component\DiscordComponentTypeEnum;
use Psr\Log\LoggerAwareTrait;

abstract class MessageComponent extends DiscordInteraction
{
    use LoggerAwareTrait;

    public function __invoke(MessageComponentInteractionDiscordEvent $interaction): void
    {
        if (
            static::getType() !== $interaction->getComponentType()
            || !preg_match(static::getCustomIdRegex(), $interaction->getCustomId(), $matches)
        ) {
            return;
        }

        $this->logger->debug(sprintf(
            'Received message component (class: %s)',
            get_called_class()
        ), [
            'user_id' => $interaction->getUserId()
        ]);

        $this->execute($this->getServices($interaction), $interaction, $matches);
    }

    abstract protected function execute(
        DiscordServiceSpool $services,
        MessageComponentInteractionDiscordEvent $interaction,
        array $regexMatches
    ): void;

    abstract protected static function getCustomIdRegex(): string;
    abstract protected static function getType(): DiscordComponentTypeEnum;
}
