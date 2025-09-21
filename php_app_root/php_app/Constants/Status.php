<?php

namespace App\Constants;

/**
 * @deprecated 建议使用更具体的状态枚举类：
 * - UserStatus::BANNED/ACTIVE 用于用户状态
 * - AdminStatus::DISABLED/ENABLED 用于管理员状态  
 * - TagStatus::DISABLED/ENABLED 用于标签状态
 * - CollectionStatus::DISABLED/ENABLED 用于合集状态
 * - SubscriptionStatus::UNSUBSCRIBED/SUBSCRIBED 用于订阅状态
 * - LinkStatus::INVALID/VALID 用于链接状态
 * 
 * 本枚举保留用于向后兼容，新代码请使用具体的状态枚举
 */
enum Status: int
{
    case INACTIVE = 0;    // 非活跃/隐藏/禁用
    case ACTIVE = 1;      // 活跃/显示/启用

    public function label(): string
    {
        return match($this) {
            self::INACTIVE => '非活跃',
            self::ACTIVE => '活跃',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    public static function fromBoolean(bool $active): self
    {
        return $active ? self::ACTIVE : self::INACTIVE;
    }

    public static function fromString(string $status): ?self
    {
        return match(strtolower($status)) {
            '1', 'active', 'true', 'on', 'yes' => self::ACTIVE,
            '0', 'inactive', 'false', 'off', 'no' => self::INACTIVE,
            default => null
        };
    }

    public static function getAllValues(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::INACTIVE->value => self::INACTIVE->label(),
        ];
    }
}