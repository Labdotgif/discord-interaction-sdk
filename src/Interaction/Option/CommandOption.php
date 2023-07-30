<?php

namespace Labdotgif\DiscordInteraction\Interaction\Option;

use Labdotgif\DiscordInteraction\LocalizationEnum;

abstract class CommandOption
{
    private CommandOptionEnum $type;
    private string $name;
    private array $nameLocalizations = [];
    private string $description;
    private array $descriptionLocalizations = [];
    private bool $isRequired = false;
    private float $minValue;
    private float $maxValue;

    final private function __construct()
    {
    }

    public static function create(string $name): static
    {
        $option = new static();

        $option->setName($name);
        $option->type = $option->getType();

        return $option;
    }

    private function setName(string $name): self
    {
        if (mb_strlen($name) > 32) {
            throw new \LengthException('Name must be less than or equal to 32 characters');
        }

        $this->name = $name;

        return $this;
    }

    public function addNameLocalization(LocalizationEnum $locale, string $name): self
    {
        if (mb_strlen($name) > 32) {
            throw new \LengthException('Name must be less than or equal to 32 characters');
        }

        $this->nameLocalizations[$locale->value] = $name;

        return $this;
    }

    public function setDescription(string $description): self
    {
        if (mb_strlen($description) > 100) {
            throw new \LengthException('Description must be less than or equal to 100 characters');
        }

        $this->description = $description;

        return $this;
    }

    public function addDescriptionLocalization(LocalizationEnum $locale, string $description): self
    {
        if (mb_strlen($description) > 100) {
            throw new \LengthException('Description must be less than or equal to 100 characters');
        }

        $this->descriptionLocalizations[$locale->value] = $description;

        return $this;
    }

    public function setRequired(bool $required = true): self
    {
        $this->isRequired = $required;

        return $this;
    }

    public function setMinValue(float $value): self
    {
        $this->minValue = $value;

        return $this;
    }

    public function setMaxValue(float $value): self
    {
        $this->maxValue = $value;

        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type->value,
            'name' => $this->name,
            'description' => $this->description,
            'required' => (int) $this->isRequired
        ];

        if (!empty($this->nameLocalizations)) {
            $data['name_localizations'] = $this->nameLocalizations;
        }

        if (!empty($this->descriptionLocalizations)) {
            $data['description_localizations'] = $this->descriptionLocalizations;
        }

        if (isset($this->minValue)) {
            $data['min_value'] = $this->minValue;
        }

        if (isset($this->maxValue)) {
            $data['max_value'] = $this->maxValue;
        }

        return $data;
    }

    abstract protected function getType(): CommandOptionEnum;
}
