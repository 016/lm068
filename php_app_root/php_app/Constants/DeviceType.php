<?php

namespace App\Constants;

enum DeviceType: int
{
    case UNKNOWN = 0;
    case DESKTOP = 1;
    case MOBILE = 2;
    case TABLET = 3;
    case BOT = 4;

    public function label(): string
    {
        return match($this) {
            self::UNKNOWN => '未知',
            self::DESKTOP => '桌面',
            self::MOBILE => '手机',
            self::TABLET => '平板',
            self::BOT => '爬虫',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::UNKNOWN => 'Unknown',
            self::DESKTOP => 'Desktop',
            self::MOBILE => 'Mobile',
            self::TABLET => 'Tablet',
            self::BOT => 'Bot',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::UNKNOWN => '❓',
            self::DESKTOP => '🖥️',
            self::MOBILE => '📱',
            self::TABLET => '📱',
            self::BOT => '🤖',
        };
    }

    public function isBot(): bool
    {
        return $this === self::BOT;
    }

    public function isMobile(): bool
    {
        return $this === self::MOBILE || $this === self::TABLET;
    }

    public static function getAllValues(): array
    {
        return [
            self::DESKTOP->value => self::DESKTOP->label(),
            self::MOBILE->value => self::MOBILE->label(),
            self::TABLET->value => self::TABLET->label(),
            self::BOT->value => self::BOT->label(),
            self::UNKNOWN->value => self::UNKNOWN->label(),
        ];
    }
}