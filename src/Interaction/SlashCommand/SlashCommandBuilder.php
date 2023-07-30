<?php

namespace Labdotgif\DiscordInteraction\Interaction\SlashCommand;

use Labdotgif\DiscordInteraction\Interaction\Command;
use Labdotgif\DiscordInteraction\Interaction\CommandTypeEnum;

class SlashCommandBuilder extends Command
{
    public static function create(): static
    {
        return new static(CommandTypeEnum::CHAT_INPUT);
    }
}
