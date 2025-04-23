<?php

namespace Labdotgif\DiscordInteraction\Event\Interaction;

use Labdotgif\DiscordInteraction\Event\DiscordEvent;

abstract class DiscordInteractionEvent extends DiscordEvent
{
    private const LANG_FALLBACK = 'en';

    private readonly string $id;
    private readonly int $type;
    private readonly string $token;
    private readonly string $userId;
    private array $rawData;
    private readonly string $channelId;
    private string $locale;

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;

        $this->id = $rawData['id'];
        $this->type = $rawData['type'];
        $this->token = $rawData['token'];
        $this->userId = $rawData['member']['user']['id'];
        $this->channelId = $rawData['channel']['id'];
        $this->locale = $rawData['locale'] ?? self::LANG_FALLBACK;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getGuildMember(?string $userId = null): ?array
    {
        if (null === $userId || $userId === $this->getUserId()) {
            return $this->rawData['member'];
        }

        if (!isset($this->rawData['data']['resolved']['members'][$userId])) {
            return null;
        }

        return $this->rawData['data']['resolved']['members'][$userId];
    }

    public function addRole(string $roleId, ?string $userId = null): void
    {
        if (null === $userId || $userId === $this->getUserId()) {
            if (!in_array($roleId, $this->getGuildMember()['roles'])) {
                $this->rawData['member']['roles'][] = $roleId;
            }
        } else {
            if (!isset($this->rawData['data']['resolved']['members'][$userId])) {
                return;
            }

            $roles = $this->rawData['data']['resolved']['members'][$userId]['roles'];

            if (!in_array($roleId, $roles)) {
                $this->rawData['data']['resolved']['members'][$userId]['roles'][] = $roleId;
            }
        }
    }

    public function removeRole(string $roleId, ?string $userId = null): void
    {
        if (null === $userId || $userId === $this->getUserId()) {
            $roles = $this->rawData['member']['roles'];

            foreach ($roles as $i => $currentRoleId) {
                if ($roleId === $currentRoleId) {
                    unset($this->rawData['member']['roles'][$i]);

                    break;
                }
            }
        } else {
            if (!isset($this->rawData['data']['resolved']['members'][$userId])) {
                return;
            }

            $roles = $this->rawData['data']['resolved']['members'][$userId]['roles'];

            foreach ($roles as $i => $currentRoleId) {
                if ($roleId === $currentRoleId) {
                    unset($this->rawData['data']['resolved']['members'][$userId]['roles'][$i]);

                    break;
                }
            }
        }
    }

    public function setRoles(array $roles, ?string $userId = null): void
    {
        if (null === $userId || $userId === $this->getUserId()) {
            $this->rawData['member']['roles'] = $roles;
        } else {
            if (!isset($this->rawData['data']['resolved']['members'][$userId])) {
                return;
            }

            $this->rawData['data']['resolved']['members'][$userId]['roles'] = $roles;
        }
    }

    public function getRoles(?string $userId = null): array
    {
        $member = $this->getGuildMember($userId);

        if (null === $member) {
            return [];
        }

        return $member['roles'];
    }
}
