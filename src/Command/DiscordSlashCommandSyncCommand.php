<?php

namespace Labdotgif\DiscordInteraction\Command;

use Labdotgif\DiscordInteraction\DiscordHttpClient;
use Labdotgif\DiscordInteraction\Interaction\CommandTypeEnum;
use Labdotgif\DiscordInteraction\Interaction\SlashCommand\SlashCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('discord:slash-command:sync')]
class DiscordSlashCommandSyncCommand extends Command
{
    private readonly string $discordGuildId;
    private readonly DiscordHttpClient $client;
    /** @var array|SlashCommand[] */
    private readonly array $slashCommands;

    public function __construct(
        string $discordGuildId,
        DiscordHttpClient $client,
        \Traversable $slashCommands
    ) {
        $this->discordGuildId = $discordGuildId;
        $this->client = $client;
        $this->slashCommands = iterator_to_array($slashCommands);

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $style->title('Discord - Slash Command - Sync');

        $applicationService = $this->client->getServices()->application;
        $isChangeMade = false;
        $remoteCommands = [];

        foreach ($applicationService->getGuildCommands(
            $this->discordGuildId
        ) as $remoteCommand) {
            $remoteCommands[$remoteCommand['name']] = $remoteCommand;
        }

        // Update & delete
        foreach ($remoteCommands as $remoteCommand) {
            // For now, only slash commands
            if (CommandTypeEnum::CHAT_INPUT->value !== $remoteCommand['type']) {
                continue;
            }

            $slashCommand = $this->slashCommands[$remoteCommand['name']] ?? null;

            // Delete
            if (null === $slashCommand) {
                $style->warning('Deleting command "' . $remoteCommand['name'] . '"');

                $isChangeMade = true;
                $applicationService->deleteGuildCommand(
                    $this->discordGuildId,
                    $remoteCommand['id']
                );
            } else if (!$slashCommand->isUpToDate($remoteCommand)) {
                // Update
                $style->info('Updating command "' . $slashCommand::getName() . '"');

                $isChangeMade = true;
                $applicationService->updateGuildCommand(
                    $this->discordGuildId,
                    $remoteCommand['id'],
                    $slashCommand
                );
            }
        }

        foreach ($this->slashCommands as $slashCommand) {
            $remoteCommand = $remoteCommands[$slashCommand::getName()] ?? null;

            if (null === $remoteCommand) {
                // Add
                $style->info('Adding command "' . $slashCommand::getName() . '"');

                $isChangeMade = true;
                $applicationService->createGuildCommand(
                    $this->discordGuildId,
                    $slashCommand
                );
            }
        }

        if (!$isChangeMade) {
            $style->success('No change made');
        }

        return static::SUCCESS;
    }
}
