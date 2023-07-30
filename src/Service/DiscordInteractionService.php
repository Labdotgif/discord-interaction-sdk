<?php

namespace Labdotgif\DiscordInteraction\Service;

use Discord\InteractionResponseType;
use Discord\InteractionType;
use Labdotgif\DiscordInteraction\Event\Interaction\DiscordInteractionEvent;
use Labdotgif\DiscordInteraction\Message\DiscordMessage;

class DiscordInteractionService extends AbstractDiscordService
{
    private const API_ROUTE_BASE = '/interactions/%s/%s';
    private const API_ROUTE_CALLBACK = self::API_ROUTE_BASE . '/callback';
    private const API_ROUTE_FOLLOW_UP = '/webhooks/%s/%s';

    public function acknowledge(
        DiscordInteractionEvent $interaction,
        bool $canShowLoader = true,
        bool $isLoaderEphemeral = true
    ): void {
        if (InteractionType::MESSAGE_COMPONENT !== $interaction->getType()) {
            $canShowLoader = true;
        }

        $data = [
            'type' => $canShowLoader
                ? InteractionResponseType::DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE
                : InteractionResponseType::DEFERRED_UPDATE_MESSAGE,
        ];

        if ($canShowLoader && $isLoaderEphemeral) {
            $data['data'] = [
                'flags' => 64
            ];
        }

        $this->post(
            sprintf(
                self::API_ROUTE_CALLBACK,
                $interaction->getId(),
                $interaction->getToken()
            ),
            [],
            $data
        );
    }

    public function respond(
        DiscordInteractionEvent $interaction,
        array|DiscordMessage $payload
    ): void {
        $this->post(
            sprintf(
                self::API_ROUTE_CALLBACK,
                $interaction->getId(),
                $interaction->getToken()
            ),
            [],
            [
                'type' => InteractionResponseType::CHANNEL_MESSAGE_WITH_SOURCE,
                'data' => [
                    $payload
                ]
            ]
        );
    }

    public function followUp(
        DiscordInteractionEvent $interaction,
        array|DiscordMessage $payload
    ): void {
        $this->post(
            sprintf(
                self::API_ROUTE_FOLLOW_UP,
                $this->getClientId(),
                $interaction->getToken()
            ),
            [],
            $payload
        );
    }
}
