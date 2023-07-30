<?php

namespace Labdotgif\DiscordInteraction\Event;

use Symfony\Component\HttpFoundation\Response;

abstract class ResponseAwareDiscordEvent extends DiscordEvent
{
    private ?Response $response = null;

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
