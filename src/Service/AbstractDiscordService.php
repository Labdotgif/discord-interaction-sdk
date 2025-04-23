<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\DiscordHttpClient;
use Labdotgif\DiscordInteraction\Message\DiscordMessage;

abstract class AbstractDiscordService
{
    private readonly DiscordHttpClient $httpClient;
    private ?string $userAccessToken;
    private bool $isUserAwareToken = false;

    public function __construct(DiscordHttpClient $httpClient, ?string $userAccessToken = null)
    {
        $this->httpClient = $httpClient;
        $this->userAccessToken = $userAccessToken;
    }

    protected function isUserTokenAware(): void
    {
        $this->isUserAwareToken = true;
    }

    protected function get(string $url, array $queries = []): array
    {
        if ($this->isUserAwareToken && null === $this->userAccessToken) {
            throw new \LogicException('Cannot execute Discord API call "' . $url . '" without an access token');
        }

        return $this->httpClient->get($url, $this->isUserAwareToken ? $this->userAccessToken : null, $queries);
    }

    protected function post(string $url, array $queries = [], array|DiscordMessage $parameters = []): array
    {
        if ($this->isUserAwareToken && null === $this->userAccessToken) {
            throw new \LogicException('Cannot execute Discord API call "' . $url . '" without an access token');
        }

        return $this->httpClient->post(
            $url,
            $this->isUserAwareToken ? $this->userAccessToken : null,
            $queries,
            $parameters
        );
    }

    protected function put(string $url, array $queries = [], array $parameters = []): array
    {
        if ($this->isUserAwareToken && null === $this->userAccessToken) {
            throw new \LogicException('Cannot execute Discord API call "' . $url . '" without an access token');
        }

        return $this->httpClient->put($url, $this->isUserAwareToken ? $this->userAccessToken : null, $queries, $parameters);
    }

    protected function patch(string $url, array $queries = [], array $parameters = []): array
    {
        if ($this->isUserAwareToken && null === $this->userAccessToken) {
            throw new \LogicException('Cannot execute Discord API call "' . $url . '" without an access token');
        }

        return $this->httpClient->patch($url, $this->isUserAwareToken ? $this->userAccessToken : null, $queries, $parameters);
    }

    protected function delete(string $url, array $queries = [], array $parameters = [], array $headers = []): array
    {
        if ($this->isUserAwareToken && null === $this->userAccessToken) {
            throw new \LogicException('Cannot execute Discord API call "' . $url . '" without an access token');
        }

        return $this->httpClient->delete(
            $url,
            $this->isUserAwareToken ? $this->userAccessToken : null,
            $queries,
            $parameters
        );
    }

    protected function getClientId(): string
    {
        return $this->httpClient->getClientId();
    }
}
