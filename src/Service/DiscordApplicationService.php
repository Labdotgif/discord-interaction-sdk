<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\Interaction\SlashCommand\SlashCommand;

class DiscordApplicationService extends AbstractDiscordService
{
    private const API_ROUTE_BASE = '/applications/%s';
    private const API_ROUTE_GUILD_COMMANDS = self::API_ROUTE_BASE . '/guilds/%s/commands';
    private const API_ROUTE_GUILD_COMMAND = self::API_ROUTE_GUILD_COMMANDS . '/%s';

    public function getGuildCommands(string $guildId): array
    {
        return $this->get(
            sprintf(
                self::API_ROUTE_GUILD_COMMANDS,
                $this->getClientId(),
                $guildId
            ),
            [
                'with_localizations' => 1
            ]
        );
    }

    public function createGuildCommand(string $guildId, SlashCommand $command): void
    {
        $this->post(
            sprintf(
                self::API_ROUTE_GUILD_COMMANDS,
                $this->getClientId(),
                $guildId
            ),
            [],
            $command->getDefinition()
        );
    }

    public function updateGuildCommand(
        string $guildId,
        string $commandId,
        SlashCommand $command
    ): void {
        $this->patch(
            sprintf(
                self::API_ROUTE_GUILD_COMMAND,
                $this->getClientId(),
                $guildId,
                $commandId
            ),
            [],
            $command->getDefinition()
        );
    }

    public function deleteGuildCommand(string $guildId, string $commandId): void
    {
        $this->delete(
            sprintf(
                self::API_ROUTE_GUILD_COMMAND,
                $this->getClientId(),
                $guildId,
                $commandId
            )
        );
    }
}
