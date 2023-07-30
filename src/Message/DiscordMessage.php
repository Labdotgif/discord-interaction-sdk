<?php

namespace Labdotgif\DiscordInteraction\Message;

use Labdotgif\DiscordInteraction\Message\Component\RowDiscordComponent;

class DiscordMessage implements \JsonSerializable
{
    private array $payload = [];
    /** @var array|RowDiscordComponent[] */
    private array $rows = [];

    public static function create(): static
    {
        return new self();
    }

    public function setUsername(string $username): self
    {
        $this->payload['username'] = $username;

        return $this;
    }

    public function setAvatar(string $url): self
    {
        $this->payload['avatar_url'] = $url;

        return $this;
    }

    public function setText(string $text): self
    {
        $this->payload['content'] = $text;

        return $this;
    }

    public function addEmbed(DiscordMessageEmbed $embed): self
    {
        $this->payload['embeds'][] = $embed;

        return $this;
    }

    public function addActionRow(RowDiscordComponent $row): static
    {
        $this->rows[] = $row;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        $data = $this->payload;

        foreach ($this->rows as $row) {
            $data['components'][] = $row->jsonSerialize();
        }

        return $data;
    }
}
