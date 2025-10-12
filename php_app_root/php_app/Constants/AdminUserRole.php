<?php

namespace App\Constants;

enum AdminUserRole: int
{
    case NORMAL = 1;           // 普通管理员
    case SUPER_ADMIN = 99;     // 超级管理员

    public function label(): string
    {
        return match($this) {
            self::NORMAL => '普通管理员',
            self::SUPER_ADMIN => '超级管理员',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::NORMAL => 'Normal Admin',
            self::SUPER_ADMIN => 'Super Admin',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    public function isNormal(): bool
    {
        return $this === self::NORMAL;
    }

    public static function getAllValues(): array
    {
        return [
            self::NORMAL->value => self::NORMAL->label(),
            self::SUPER_ADMIN->value => self::SUPER_ADMIN->label(),
        ];
    }

    /**
     * 检查是否有管理员管理权限
     */
    public function canManageAdmins(): bool
    {
        return $this->value >= self::SUPER_ADMIN->value;
    }
}
