<?php

namespace Labdotgif\DiscordInteraction\Interaction;

enum CommandTypeEnum: int
{
    case CHAT_INPUT = 1;
    case USER = 2;
    case MESSAGE = 3;
}
