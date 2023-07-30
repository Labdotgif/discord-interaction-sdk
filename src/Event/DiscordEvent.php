<?php

namespace Labdotgif\DiscordInteraction\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DiscordEvent extends Event implements DiscordEventInterface
{
    protected Request $request;

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
