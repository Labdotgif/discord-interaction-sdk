<?php

namespace Labdotgif\DiscordInteraction\Interaction\Option;

class UserCommandOption extends CommandOption
{
    protected function getType(): CommandOptionEnum
    {
        return CommandOptionEnum::USER;
    }
}
