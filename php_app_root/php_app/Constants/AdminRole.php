<?php

namespace App\Constants;

enum AdminRole: int
{
    case SUPER_ADMIN = 99;     // 超级管理员

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => '超级管理员',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrator',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    public static function getAllValues(): array
    {
        return [
            self::SUPER_ADMIN->value => self::SUPER_ADMIN->label(),
        ];
    }
}