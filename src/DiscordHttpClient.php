<?php

namespace Labdotgif\DiscordInteraction;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use GuzzleRetry\GuzzleRetryMiddleware;
use Labdotgif\DiscordInteraction\Exception\DiscordException;
use Labdotgif\DiscordInteraction\Message\DiscordMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DiscordHttpClient
{
    private const BASE_URL = 'https://discord.com/api/v10';

    public const SCOPE_IDENTIFY = 'identify';
    public const SCOPE_GUILDS_JOIN = 'guilds.join';
    public const SCOPE_GUILDS = 'guilds';
    public const SCOPE_CONNECTIONS = 'connections';

    private readonly HttpClient $httpClient;
    private readonly string $env;
    private readonly string $clientId;
    private readonly string $clientSecret;
    private readonly string $botToken;
    private DiscordServiceSpool $services;

    public function __construct(
        string $env,
        string $clientId,
        string $clientSecret,
        string $botToken,
        HttpClient $httpClient = null
    ) {
        if (null === $httpClient) {
            $stack = HandlerStack::create();
            $stack->push(GuzzleRetryMiddleware::factory([
                'max_retry_attempts' => 5,
                'retry_on_timeout' => true,
                'retry_only_if_retry_after_header' => true
            ]));

            $httpClient = new HttpClient([
                'timeout' => 3,
                'handler' => $stack
            ]);
        }

        $this->httpClient = $httpClient;
        $this->env = $env;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->botToken = $botToken;
    }

    public function authenticate(
        string $authorizeUrl,
        string $scope,
        DiscordAccessToken $accessToken = null,
    ): ?DiscordServiceSpool {
        if (null === $accessToken || !$accessToken->isScopesEqual($scope)) {
            $this->redirect($authorizeUrl, $scope);
        } else if (!$accessToken->isValid()) {
            try {
                $this->refreshToken($accessToken);
            } catch (DiscordException) {
                $this->redirect($authorizeUrl, $scope);
            }
        }

        return $this->getServices($accessToken);
    }

    private function redirect(string $authorizeUrl, string $scope): never
    {
        (new RedirectResponse($this->getOAuthUrl($authorizeUrl, $scope)))->send();

        exit;
    }

    public function get(string $url, ?string $userAccessToken, array $queries = [], bool $isBot = true): array
    {
        return $this->send('get', $url, $userAccessToken, $queries);
    }

    public function put(string $url, ?string $userAccessToken, array $queries = [], array $parameters = []): array
    {
        return $this->send('put', $url, $userAccessToken, $queries, $parameters);
    }

    public function patch(string $url, ?string $userAccessToken, array $queries = [], array $parameters = []): array
    {
        return $this->send('patch', $url, $userAccessToken, $queries, $parameters);
    }

    public function post(string $url, ?string $userAccessToken, array $queries = [], array|DiscordMessage $parameters = []): array
    {
        return $this->send('post', $url, $userAccessToken, $queries, $parameters);
    }

    public function delete(
        string $url,
        ?string $userAccessToken,
        array $queries = [],
        array $parameters = [],
        array $headers = []
    ): array {
        return $this->send('delete', $url, $userAccessToken, $queries, $parameters, $headers);
    }

    private function send(
        string $httpMethod,
        string $url,
        ?string $userAccessToken,
        array $queries = [],
        array|DiscordMessage $parameters = [],
        array $headers = []
    ): array {
        $data = [
            RequestOptions::QUERY => $queries,
            RequestOptions::HEADERS => [
                'Authorization' => null === $userAccessToken
                    ? 'Bot ' . $this->botToken
                    : 'Bearer ' . $userAccessToken
            ]
        ];

        if ('get' !== $httpMethod) {
            $data[RequestOptions::JSON] = $parameters;
        }

        if (!empty($headers)) {
            $data[RequestOptions::HEADERS] = $headers;
        }

        try {
            $response = $this->httpClient->{$httpMethod}(self::BASE_URL . $url, $data);
        } catch (ClientException $e) {
            throw new DiscordException($e->getMessage(), $e->getCode());
        }

        if (in_array($response->getStatusCode(), [Response::HTTP_NO_CONTENT, Response::HTTP_CREATED])) {
            return [
                'status_code' => Response::HTTP_NO_CONTENT
            ];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getOAuthUrl(string $redirectUri, string $scope): string
    {
        return 'https://discord.com/api/oauth2/authorize?' . http_build_query([
            'client_id' => $this->getClientId(),
            'redirect_uri' => 'https://' . ('dev' === $this->env ? 'local' : 'www') . '.instant-gaming.com/' . $redirectUri,
            'response_type' => 'code',
            'scope' => $scope
        ]);
    }

    public function authorize(
        string $code,
        string $redirectUri,
        string $scope,
        DiscordAccessToken $accessToken = null
    ): DiscordAccessToken {
        try {
            $response = $this->httpClient->post(self::BASE_URL . '/oauth2/token', [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                    'scope' => $scope
                ]
            ]);
        } catch (ClientException $e) {
            throw new DiscordException($e->getMessage(), $e->getCode());
        }

        $content = json_decode((string) $response->getBody(), true);

        if (null === $accessToken || !$accessToken->isScopesEqual($scope)) {
            $accessToken = new DiscordAccessToken(
                $content['access_token'],
                $content['expires_in'],
                $content['refresh_token'],
                $scope
            );
        } else {
            $accessToken
                ->setAccessToken($content['access_token'])
                ->setRefreshToken($content['refresh_token'])
                ->setExpiresIn($content['expires_in']);
        }

        return $accessToken;
    }

    public function refreshToken(DiscordAccessToken $accessToken): void
    {
        try {
            $response = $this->httpClient->post(self::BASE_URL . '/oauth2/token', [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $accessToken->getRefreshToken()
                ]
            ]);
        } catch (ClientException $e) {
            throw new DiscordException($e->getMessage(), $e->getCode());
        }

        $content = json_decode((string) $response->getBody(), true);

        $accessToken
            ->setAccessToken($content['access_token'])
            ->setRefreshToken($content['refresh_token'])
            ->setExpiresIn($content['expires_in']);
    }

    public function getServices(DiscordAccessToken $accessToken = null): DiscordServiceSpool
    {
        if (!isset($this->services) || null !== $accessToken) {
            if (null !== $accessToken) {
                return new DiscordServiceSpool($this, $accessToken->getAccessToken());
            }

            $this->services = new DiscordServiceSpool($this, $accessToken);
        }

        return $this->services;
    }

    public static function getScopeAsString(...$scopes): string
    {
        sort($scopes);

        return implode(' ', $scopes);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }
}
