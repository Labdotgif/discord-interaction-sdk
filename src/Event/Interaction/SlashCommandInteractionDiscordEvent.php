<?php

namespace Labdotgif\DiscordInteraction\Event\Interaction;

class SlashCommandInteractionDiscordEvent extends DiscordInteractionEvent
{
    public const EVENT_NAME = 'interaction.slash_command';

    private readonly string $commandName;
    private  array $options;

    public function __construct(string $commandName, array $rawData)
    {
        parent::__construct($rawData);

        $this->commandName = $commandName;

        if (isset($rawData['data']['options'])) {
            $this->options = [];

            foreach ($rawData['data']['options'] as $option) {
                $this->options[$option['name']] = $option;
            }
        }
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function getOption(string $name): ?string
    {
        if (!isset($this->options[$name])) {
            return null;
        }

        return $this->options[$name]['value'];
    }

    public static function getEventName(): string
    {
        return static::EVENT_NAME;
    }
}
