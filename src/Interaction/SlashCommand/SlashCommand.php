<?php

namespace Labdotgif\DiscordInteraction\Interaction\SlashCommand;

use Labdotgif\DiscordInteraction\DiscordHttpClient;
use Labdotgif\DiscordInteraction\DiscordServiceSpool;
use Labdotgif\DiscordInteraction\Event\Interaction\SlashCommandInteractionDiscordEvent;
use Labdotgif\DiscordInteraction\Interaction\DiscordInteraction;
use Psr\Log\LoggerAwareTrait;

abstract class SlashCommand extends DiscordInteraction
{
    use LoggerAwareTrait;

    private SlashCommandBuilder $builder;
    private DiscordHttpClient $discordClient;
    private DiscordServiceSpool $services;

    public function getDefinition(): array
    {
        if (!isset($this->builder)) {
            $this->builder = SlashCommandBuilder::create()
                ->setName(static::getName());

            $this->configure($this->builder);

            if (!isset($this->builder->toArray()['name'])) {
                throw new \InvalidArgumentException('Slash command must have a name');
            }
        }

        return array_merge([
            'nsfw' => false,
            'default_member_permissions' => null,
            'name_localizations' => null,
            'description' => null,
            'description_localizations' => null,
            'options' => null
        ], $this->builder->toArray());
    }

    public function isUpToDate(array $remoteCommand): bool
    {
        unset(
            $remoteCommand['id'],
            $remoteCommand['application_id'],
            $remoteCommand['version'],
            $remoteCommand['guild_id'],
            $remoteCommand['default_permission'],
        );

        $localCommand = $this->getDefinition();

        if (null === $localCommand['options']) {
            $remoteCommand['options'] = null;
        }

        ksort($remoteCommand);
        ksort($localCommand);

        return $remoteCommand === $localCommand;
    }

    public function __invoke(SlashCommandInteractionDiscordEvent $interaction): void
    {
        if (static::getName() !== $interaction->getCommandName()) {
            return;
        }

        $this->logger->debug(sprintf(
            'Received slash command (class: %s)',
            get_called_class()
        ), [
            'user_id' => $interaction->getUserId()
        ]);

        $this->execute($this->getServices($interaction), $interaction);
    }

    abstract public function configure(SlashCommandBuilder $builder): void;

    abstract public function execute(
        DiscordServiceSpool $services,
        SlashCommandInteractionDiscordEvent $interaction
    ): void;

    abstract public static function getName(): string;
}
