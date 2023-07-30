<?php

namespace Labdotgif\DiscordInteraction\Event\Interaction;

use Labdotgif\DiscordInteraction\Event\ResponseAwareDiscordEvent;

class AckPingInteractionDiscordEvent extends ResponseAwareDiscordEvent
{
    public function __construct(
        private readonly array $raw
    ) {
    }

    public function getSignatureFingerprint(): ?string
    {
        return $this->request->headers->get('X_SIGNATURE_ED25519');
    }

    public function getSignatureTimestamp(): int
    {
        return (int) $this->request->headers->get('X_SIGNATURE_TIMESTAMP');
    }

    public function getBody(): string
    {
        return json_encode($this->raw);
    }

    public static function getEventName(): string
    {
        return 'interaction.ack_ping';
    }
}
