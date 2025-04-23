<?php

namespace Labdotgif\DiscordInteraction\Message;

class DiscordMessageEmbed implements \JsonSerializable
{
    private array $content = [];

    final public function __construct()
    {
    }

    public static function create(): self
    {
        return new static();
    }

    public function setAuthor(string $name, ?string $url = null, string $iconUrl = null): self
    {
        $this->content['author'] = [
            'name' => $name
        ];

        if (null !== $url) {
            $this->content['author']['url'] = $url;
        }

        if (null !== $iconUrl) {
            $this->content['author']['icon_url'] = $iconUrl;
        }

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->content['title'] = $title;

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->content['url'] = $url;

        if (!isset($this->content['title'])) {
            $this->content['title'] = $url;
        }

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->content['description'] = $description;

        return $this;
    }

    public function setColor(string $color, $isHex = true): self
    {
        $this->content['color'] = $isHex ? hexdec($color) : $color;

        return $this;
    }

    public function addField(string $name, string $value, bool $isInline = true): self
    {
        $this->content['fields'][] = [
            'name' => $name,
            'value' => $value,
            'inline' => $isInline
        ];

        return $this;
    }

    public function setThumbnail(string $url): self
    {
        $this->content['thumbnail']['url'] = $url;

        return $this;
    }

    public function setImage(string $url): self
    {
        $this->content['image']['url'] = $url;

        return $this;
    }

    public function setFooter(string $text, ?string $iconUrl = null): self
    {
        $this->content['footer']['text'] = $text;

        if (null !== $iconUrl) {
            $this->content['footer']['icon_url'] = $iconUrl;
        }

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->content;
    }
}
