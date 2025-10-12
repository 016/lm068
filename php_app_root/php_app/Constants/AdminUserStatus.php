<?php

namespace App\Constants;

enum AdminUserStatus: int
{
    case DISABLED = 0;     // 禁用
    case ENABLED = 1;      // 启用

    public function label(): string
    {
        return match($this) {
            self::DISABLED => '禁用',
            self::ENABLED => '启用',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::DISABLED => 'Disabled',
            self::ENABLED => 'Enabled',
        };
    }

    public function isEnabled(): bool
    {
        return $this === self::ENABLED;
    }

    public function isDisabled(): bool
    {
        return $this === self::DISABLED;
    }

    public static function fromBoolean(bool $enabled): self
    {
        return $enabled ? self::ENABLED : self::DISABLED;
    }

    public static function getAllValues(): array
    {
        return [
            self::ENABLED->value => self::ENABLED->label(),
            self::DISABLED->value => self::DISABLED->label(),
        ];
    }

    public static function getVisibleStatuses(): array
    {
        return [
            self::ENABLED->value
        ];
    }
}
