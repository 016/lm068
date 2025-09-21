<?php

namespace App\Constants;

enum UserStatus: int
{
    case BANNED = 0;       // 不可用/封停
    case ACTIVE = 1;       // 可用/正常

    public function label(): string
    {
        return match($this) {
            self::BANNED => '封停',
            self::ACTIVE => '正常',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::BANNED => 'Banned',
            self::ACTIVE => 'Active',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isBanned(): bool
    {
        return $this === self::BANNED;
    }

    public static function fromBoolean(bool $active): self
    {
        return $active ? self::ACTIVE : self::BANNED;
    }

    public static function getAllValues(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::BANNED->value => self::BANNED->label(),
        ];
    }
}