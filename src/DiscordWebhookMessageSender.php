<?php

namespace Labdotgif\DiscordInteraction;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Labdotgif\DiscordInteraction\Message\DiscordMessage;

class DiscordWebhookMessageSender
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    public function send(string $webhookUrl, DiscordMessage $message): bool
    {
        try {
            $this->httpClient->post($webhookUrl, [
                RequestOptions::JSON => $message
            ]);
        } catch (ClientException) {
            return false;
        }

        return true;
    }
}
