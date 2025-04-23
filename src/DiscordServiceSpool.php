<?php

namespace Labdotgif\DiscordInteraction;

use Labdotgif\DiscordInteraction\Service\DiscordApplicationService;
use Labdotgif\DiscordInteraction\Service\DiscordChannelService;
use Labdotgif\DiscordInteraction\Service\DiscordGuildService;
use Labdotgif\DiscordInteraction\Service\DiscordUserService;
use Labdotgif\DiscordInteraction\Service\DiscordInteractionService;

class DiscordServiceSpool
{
    public readonly ?string $userAccessToken;
    public readonly DiscordUserService $user;
    public readonly DiscordGuildService $guilds;
    public readonly DiscordApplicationService $application;
    public readonly DiscordInteractionService $interaction;
    public readonly DiscordChannelService $channel;

    public function __construct(DiscordHttpClient $httpClient, ?string $userAccessToken = null)
    {
        $this->userAccessToken = $userAccessToken;

        $this->user = new DiscordUserService($httpClient, $userAccessToken);
        $this->guilds = new DiscordGuildService($httpClient, $userAccessToken);
        $this->application = new DiscordApplicationService($httpClient, $userAccessToken);
        $this->interaction = new DiscordInteractionService($httpClient, $userAccessToken);
        $this->channel = new DiscordChannelService($httpClient, $userAccessToken);
    }

    public function getUserAccessToken(): string
    {
        return $this->userAccessToken;
    }
}
