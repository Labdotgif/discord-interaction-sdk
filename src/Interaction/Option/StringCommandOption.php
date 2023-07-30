<?php

namespace Labdotgif\DiscordInteraction\Interaction\Option;

class StringCommandOption extends CommandOption
{
    protected function getType(): CommandOptionEnum
    {
        return CommandOptionEnum::STRING;
    }
}
