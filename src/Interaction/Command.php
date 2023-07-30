<?php

namespace Labdotgif\DiscordInteraction\Interaction;

use Labdotgif\DiscordInteraction\Interaction\Option\CommandOption;
use Labdotgif\DiscordInteraction\LocalizationEnum;

abstract class Command
{
    private CommandTypeEnum $type;
    private string $name;
    private array $nameLocalizations = [];
    private ?string $description = null;
    private array $descriptionLocalizations = [];
    /** @var array|CommandOption[] */
    private array $options = [];

    final protected function __construct(CommandTypeEnum $type)
    {
        $this->type = $type;
    }

    public function setName(string $name): static
    {
        $this->name = $this->validateName($name);

        return $this;
    }


    public function setNameLocalization(LocalizationEnum $locale, string $name): static
    {
        $this->nameLocalizations[$locale->value] = $this->validateName($name);

        return $this;
    }

    public function setDescription(string $description): static
    {
        $descriptionLen = mb_strlen($description);

        if ($descriptionLen < 1) {
            throw new \LengthException('Command description can not be empty');
        }

        if ($descriptionLen > 100) {
            throw new \LengthException('Command description can be only up to 100 characters long');
        }

        $this->description = $description;

        return $this;
    }

    public function setDescriptionLocalization(LocalizationEnum $locale, ?string $description): self
    {
        if (mb_strlen($description) > 100) {
            throw new \LengthException('Command description must be less than or equal to 100 characters');
        }

        $this->descriptionLocalizations[$locale->value] = $description;

        return $this;
    }

    public function addOption(CommandOption $option): self
    {
        if ($this->type !== CommandTypeEnum::CHAT_INPUT) {
            throw new \DomainException('Option can only be added on CHAT_INPUT command');
        }

        if (isset($this->options) && count($this->options) >= 25) {
            throw new \OverflowException('Command can only have a maximum of 25 options');
        }

        $this->options[] = $option;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type->value,
            'name' => $this->name,
            'description' => $this->description
        ];

        if (!empty($this->nameLocalizations)) {
            $data['name_localizations'] = $this->nameLocalizations;
        }

        if (!empty($this->descriptionLocalizations)) {
            $data['description_localizations'] = $this->descriptionLocalizations;
        }

        if (!empty($this->options)) {
            $data['options'] = [];

            foreach ($this->options as $option) {
                $data['options'][] = $option->toArray();
            }
        }

        return $data;
    }

    private function validateName(string $name): string
    {
        $length = mb_strlen($name);

        if ($length < 1) {
            throw new \LengthException('Command name can not be empty');
        }

        if ($length > 32) {
            throw new \LengthException('Command name can be only up to 32 characters long');
        }

        if (!preg_match('/^[-_\p{L}\p{N}\p{Devanagari}\p{Thai}]{1,32}$/u', $name)) {
            throw new \DomainException('Slash command name contains invalid characters');
        }

        return $name;
    }

    abstract public static function create(): static;
}
