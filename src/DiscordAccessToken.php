<?php

namespace Labdotgif\DiscordInteraction;

class DiscordAccessToken
{
    private string $accessToken;
    private int $expiresAt;
    private string $refreshToken;
    private readonly array $scopes;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        string $refreshToken,
        string $scopes
    ) {
        $this->accessToken = $accessToken;
        $this->expiresAt = time() + $expiresIn;
        $this->refreshToken = $refreshToken;
        $this->scopes = explode(' ', $scopes);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getScopesAsString(): string
    {
        return implode(' ', $this->scopes);
    }

    public function isValid(): bool
    {
        // We minus 10 because we may have some work to do before using the access token
        return $this->expiresAt > (time() - 10);
    }

    public function setExpiresIn(int $expiresIn): static
    {
        $this->expiresAt = time() + $expiresIn;

        return $this;
    }

    public function isScopesEqual(string|array $scopes): bool
    {
        if (!is_array($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        $localScope = $this->scopes;

        sort($scopes);
        sort($localScope);

        return $scopes === $localScope;
    }
}
