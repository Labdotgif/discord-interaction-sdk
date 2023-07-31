<?php

namespace Labdotgif\DiscordInteraction\Interaction;

use Labdotgif\DiscordInteraction\DiscordHttpClient;
use Labdotgif\DiscordInteraction\DiscordServiceSpool;
use Labdotgif\DiscordInteraction\Event\Interaction\DiscordInteractionEvent;
use Labdotgif\DiscordInteraction\Message\DiscordMessage;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\Service\Attribute\Required;

abstract class DiscordInteraction implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private DiscordHttpClient $discordClient;
    private DiscordServiceSpool $services;

    protected function getServices(DiscordInteractionEvent $interaction): ?DiscordServiceSpool
    {
        $this->services = $this->discordClient->getServices();

        if ($this->isAcknowledgable()) {
            // Leave us 15 minutes to respond
            $this->acknowledge($interaction);
        }

        return $this->services;
    }

    protected function respond(DiscordInteractionEvent $interaction, string|DiscordMessage $message): void
    {
        if (is_string($message)) {
            $message = [
                'content' => $message
            ];
        }

        if ($this->isAcknowledgable()) {
            $this->services->interaction->followUp($interaction, $message);
        } else {
            $this->services->interaction->respond($interaction, $message);
        }
    }

    protected function isAcknowledgable(): bool
    {
        return true;
    }

    protected function isAcknowledgeEphemeral(): bool
    {
        return true;
    }

    protected function getLocale(DiscordInteractionEvent $event): string
    {
        $lang = preg_replace('/-.+/', '', $event->getLocale());

        if (!in_array($lang, ['fr', 'pt', 'es', 'it', 'en', 'de', 'pl'])) {
            $lang = 'en';
        }

        return $lang;
    }

    protected function acknowledge(DiscordInteractionEvent $interaction): void
    {
        $this->services->interaction->acknowledge($interaction, isLoaderEphemeral: $this->isAcknowledgeEphemeral());
    }

    #[Required]
    public function setDiscordClient(DiscordHttpClient $discordClient): void
    {
        $this->discordClient = $discordClient;
    }
}
