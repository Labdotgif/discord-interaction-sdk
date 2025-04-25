<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\Message\DiscordMessage;

class DiscordChannelService extends AbstractDiscordService
{
    /** @see https://discord.com/developers/docs/resources/channel#create-message */
    private const API_ROUTE_MESSAGE_CREATE = '/channels/%s/messages';
    private const API_ROUTE_PINS = '/channels/%s/pins/%s';

    public function createMessage(string $channelId, DiscordMessage $message): array
    {
        return $this->post(sprintf(self::API_ROUTE_MESSAGE_CREATE, $channelId), parameters: $message);
    }

    public function pinMessage(string $channelId, string $messageId): array
    {
        return $this->put(sprintf(self::API_ROUTE_PINS, $channelId, $messageId));
    }
}
