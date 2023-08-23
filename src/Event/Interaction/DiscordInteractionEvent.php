<?php

namespace Labdotgif\DiscordInteraction\Event\Interaction;

use Labdotgif\DiscordInteraction\Event\DiscordEvent;

abstract class DiscordInteractionEvent extends DiscordEvent
{
    private const LANG_FALLBACK = 'en';

    private readonly string $id;
    private readonly int $type;
    private readonly string $token;
    private readonly string $userId;
    private readonly array $rawData;
    private readonly string $channelId;
    private string $locale;

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;

        $this->id = $rawData['id'];
        $this->type = $rawData['type'];
        $this->token = $rawData['token'];
        $this->userId = $rawData['member']['user']['id'];
        $this->channelId = $rawData['channel']['id'];
        $this->locale = $rawData['locale'] ?? self::LANG_FALLBACK;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getGuildMember(): array
    {
        return $this->rawData['member'];
    }
}
