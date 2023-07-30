<?php

namespace Labdotgif\DiscordInteraction\Message\Component;

class RowDiscordComponent extends DiscordComponent
{
    /** @var array|DiscordComponent[] */
    private array $components = [];

    final private function __construct()
    {
    }

    public static function create(): static
    {
        return new static();
    }

    public function add(DiscordComponent $component): static
    {
        if (count($this->components) >= 5) {
            throw new \LogicException('Cannot add more than 5 actions in a single row');
        }

        $this->components[] = $component;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        foreach ($this->components as $component) {
            $data['components'][] = $component->jsonSerialize();
        }

        return $data;
    }

    protected function getType(): DiscordComponentTypeEnum
    {
        return DiscordComponentTypeEnum::ACTION_ROW;
    }
}
