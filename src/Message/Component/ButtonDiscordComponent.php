<?php

namespace Labdotgif\DiscordInteraction\Message\Component;

class ButtonDiscordComponent extends DiscordComponent
{
    final private function __construct()
    {
    }

    public static function create(?string $label = null): static
    {
        $button = new static();

        if (null !== $label) {
            $button->setLabel($label);
        }

        return $button;
    }

    public function setColor(ButtonDiscordStyleEnum $color): static
    {
        if (isset($this->payload['style']) && ButtonDiscordStyleEnum::LINK->value === $this->payload['style']) {
            return $this;
        }

        return $this->set('style', $color->value);
    }

    public function setLabel(string $label): static
    {
        return $this->set('label', $label);
    }

    public function setLink(string $url): static
    {
        unset($this->payload['custom_id']);

        $this->set('style', ButtonDiscordStyleEnum::LINK->value);

        return $this->set('url', $url);
    }

    public function setCustomId(string $id): static
    {
        if (
            isset($this->payload['style'])
            && ButtonDiscordStyleEnum::LINK === ButtonDiscordStyleEnum::from($this->payload['style'])
        ) {
            throw new \LogicException('Cannot set a custom_id to a button when a link has been set');
        }

        return $this->set('custom_id', $id);
    }

    public function isDisabled(): static
    {
        return $this->set('disabled', 1);
    }

    protected function getType(): DiscordComponentTypeEnum
    {
        return DiscordComponentTypeEnum::BUTTON;
    }
}
