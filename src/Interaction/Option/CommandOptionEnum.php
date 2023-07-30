<?php

namespace Labdotgif\DiscordInteraction\Interaction\Option;

enum CommandOptionEnum: int
{
    case SUB_COMMAND = 1;
    case SUB_COMMAND_GROUP = 2;
    case STRING = 3;
    case INTEGER = 4; // Any integer between -2^53 and 2^53
    case BOOLEAN = 5;
    case USER = 6;
    case CHANNEL = 7; // Includes all channel types + categories
    case ROLE = 8;
    case MENTIONABLE = 9; // Includes users and roles
    case NUMBER = 10; // Any double between -2^53 and 2^53
    case ATTACHMENT = 11;
}
