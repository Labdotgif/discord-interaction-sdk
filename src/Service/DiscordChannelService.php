<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\Message\DiscordMessage;

class DiscordChannelService extends AbstractDiscordService
{
    /** @see https://discord.com/developers/docs/resources/channel#create-message */
    private const API_ROUTE_MESSAGE_CREATE = '/channels/%s/messages';

    public function createMessage(string $channelId, DiscordMessage $message): array
    {
        return $this->post(sprintf(self::API_ROUTE_MESSAGE_CREATE, $channelId), parameters: $message);
    }
}
