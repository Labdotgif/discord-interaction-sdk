<?php

namespace Labdotgif\DiscordInteraction\Event;

use Symfony\Component\HttpFoundation\Request;

interface DiscordEventInterface
{
    public function setRequest(Request $request): void;

    public static function getEventName(): string;
}
