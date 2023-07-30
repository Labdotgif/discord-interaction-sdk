<?php

namespace Labdotgif\DiscordInteraction\Service;

use Labdotgif\DiscordInteraction\Exception\DiscordException;
use Symfony\Component\HttpFoundation\Response;

class DiscordGuildService extends AbstractDiscordService
{
    private const API_ROUTE_FROM_INVITATION = '/invite/%s';
    /** @see https://discord.com/developers/docs/resources/guild#add-guild-member */
    private const API_ROUTE_GUILD_MEMBERS = '/guilds/%s/members';
    private const API_ROUTE_GUILD_MEMBER = self::API_ROUTE_GUILD_MEMBERS . '/%s';
    private const API_ROUTE_GUILD_MEMBER_ROLE = '/guilds/%s/members/%s/roles/%s';

    public function findOneByInvitationLink(string $invitationLink): ?array
    {
        $linkParts = explode('/', $invitationLink);

        return $this->findOneByInvitationCode(array_pop($linkParts));
    }

    public function findOneByInvitationCode(string $invitationCode): ?array
    {
        try {
            return $this->get(
                sprintf(
                    self::API_ROUTE_FROM_INVITATION,
                    $invitationCode
                )
            );
        } catch (DiscordException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getCode()) {
                return null;
            }

            throw $e;
        }
    }

    public function getMembers(string $guildId, int $limit = 100, string $afterUserId = null): ?array
    {
        $query = [
            'limit' => $limit
        ];

        if (null !== $afterUserId) {
            $query['after'] = $afterUserId;
        }

        return $this->get(
            sprintf(
                self::API_ROUTE_GUILD_MEMBERS,
                $guildId
            ),
            $query
        );
    }

    public function findOneMemberById(string $guildId, string $memberId): ?array
    {
        try {
            return $this->get(
                sprintf(
                    self::API_ROUTE_GUILD_MEMBER,
                    $guildId,
                    $memberId
                )
            );
        } catch (DiscordException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getCode()) {
                return null;
            }

            throw $e;
        }
    }

    public function addRole(string $guildId, string $memberId, string|array $roles, array $guildMember = null): bool
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (null === $guildMember) {
            $guildMember = $this->findOneMemberById($guildId, $memberId);

            if (null === $guildMember) {
                return false;
            }
        }

        $finalRoles = [];

        foreach ($roles as $role) {
            if (!in_array($role, $guildMember['roles'])) {
                $finalRoles[] = $role;
            }
        }

        if (empty($finalRoles)) {
            return true;
        }

        try {
            $this->patch(
                sprintf(
                    self::API_ROUTE_GUILD_MEMBER,
                    $guildId,
                    $memberId
                ),
                [],
                [
                    'roles' => array_merge($guildMember['roles'], $roles)
                ]
            );

            return true;
        } catch (DiscordException $e) {
            return false;
        }
    }

    public function removeRole(string $guildId, string $memberId, string $roleId): bool
    {
        try {
            $response = $this->delete(
                sprintf(
                    self::API_ROUTE_GUILD_MEMBER_ROLE,
                    $guildId,
                    $memberId,
                    $roleId
                )
            );
        } catch (DiscordException $e) {
            return false;
        }

        return Response::HTTP_NO_CONTENT === $response['status_code'];
    }

    /**
     * @require CREATE_INSTANT_INVITE bot permission
     * @require MANAGE_ROLES bot permission (if $roles is not empty)
     */
    public function join(string $guildId, string $userId, string $userAccessToken, array $roles = []): bool
    {
        $response = $this->put(
            sprintf(
                self::API_ROUTE_GUILD_MEMBER,
                $guildId,
                $userId
            ),
            parameters: [
                'access_token' => $userAccessToken,
                'roles' => $roles
            ]
        );

        return Response::HTTP_CREATED === $response['status_code'];
    }
}
