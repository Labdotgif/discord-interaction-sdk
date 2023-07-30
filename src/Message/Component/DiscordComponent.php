<?php

namespace Labdotgif\DiscordInteraction\Message\Component;

abstract class DiscordComponent implements \JsonSerializable
{
    protected array $payload = [];

    public function jsonSerialize(): array
    {
        return array_merge($this->payload, [
            'type' => $this->getType()->value
        ]);
    }

    protected function set(string $name, mixed $value): static
    {
        $this->payload[$name] = $value;

        return $this;
    }

    abstract protected function getType(): DiscordComponentTypeEnum;
}
