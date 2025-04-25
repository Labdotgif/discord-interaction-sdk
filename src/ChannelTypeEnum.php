<?php

namespace Labdotgif\DiscordInteraction;

enum ChannelTypeEnum: int
{
    case GUILD_TEXT = 0;
    case GUILD_VOICE = 2;
    case GUILD_ANNOUNCEMENT = 5;
    case GUILD_STAGE_VOICE = 13;
    case GUILD_FORUM = 15;
}
