<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\DiscordHttpClient;

class DiscordUserService extends AbstractDiscordService
{
    private const API_ROUTE_ME = '/users/@me';
    private const API_ROUTE_ME_GUILDS = self::API_ROUTE_ME . '/guilds';

    public function __construct(DiscordHttpClient $httpClient, string $userAccessToken = null)
    {
        parent::__construct($httpClient, $userAccessToken);

        $this->isUserTokenAware();
    }

    public function me(): array
    {
        return $this->get(self::API_ROUTE_ME);
    }

    public function guilds(): array
    {
        return $this->get(self::API_ROUTE_ME_GUILDS);
    }
}
