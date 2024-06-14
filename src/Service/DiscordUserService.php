<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\DiscordHttpClient;

class DiscordUserService extends AbstractDiscordService
{
    private const API_ROUTE_ME = '/users/@me';
    private const API_ROUTE_ME_GUILDS = self::API_ROUTE_ME . '/guilds';

    public const int PERMISSION_ADMINISTRATOR = 1 << 3;
    public const int PERMISSION_MANAGE_GUILD = 1 << 5;

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

    public function hasPermission(string $userPermissions, int $permission): bool
    {
        return $userPermissions & $permission;
    }
}
