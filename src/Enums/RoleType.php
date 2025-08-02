<?php

namespace Danielpk74\LaravelAuthStarter\Enums;

enum RoleType: int
{
    case Admin = 1;
    case User = 2;

    /**
     * Get all available roles as an array
     */
    public static function toArray(): array
    {
        return [
            'admin' => self::Admin->value,
            'user' => self::User->value,
        ];
    }

    /**
     * Get role name
     */
    public function getName(): string
    {
        return match($this) {
            self::Admin => 'admin',
            self::User => 'user',
        };
    }

    /**
     * Get role label for display
     */
    public function getLabel(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
        };
    }

    /**
     * Check if role is admin
     */
    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    /**
     * Check if role is user
     */
    public function isUser(): bool
    {
        return $this === self::User;
    }
}
