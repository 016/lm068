<?php

namespace App\Constants;

enum OsFamily: int
{
    case UNKNOWN = 0;
    case WINDOWS = 1;
    case IOS = 2;
    case ANDROID = 3;
    case LINUX = 4;
    case MACOS = 5;
    case CHROMEOS = 6;
    case UNIX = 7;
    case BSD = 8;
    case SYMBIAN = 9;
    case BLACKBERRY = 10;
    case WINDOWS_PHONE = 11;
    case FIRE_OS = 12;

    public function label(): string
    {
        return match($this) {
            self::UNKNOWN => '未知',
            self::WINDOWS => 'Windows',
            self::IOS => 'iOS',
            self::ANDROID => 'Android',
            self::LINUX => 'Linux',
            self::MACOS => 'macOS',
            self::CHROMEOS => 'ChromeOS',
            self::UNIX => 'Unix',
            self::BSD => 'BSD',
            self::SYMBIAN => 'Symbian',
            self::BLACKBERRY => 'BlackBerry',
            self::WINDOWS_PHONE => 'Windows Phone',
            self::FIRE_OS => 'Fire OS',
        };
    }

    public function englishLabel(): string
    {
        return $this->label(); // 操作系统名称通常不翻译
    }

    public function icon(): string
    {
        return match($this) {
            self::UNKNOWN => '❓',
            self::WINDOWS => '🪟',
            self::IOS => '🍎',
            self::ANDROID => '🤖',
            self::LINUX => '🐧',
            self::MACOS => '🍎',
            self::CHROMEOS => '🔵',
            self::UNIX => '🖥️',
            self::BSD => '😈',
            self::SYMBIAN => '📱',
            self::BLACKBERRY => '📱',
            self::WINDOWS_PHONE => '📱',
            self::FIRE_OS => '🔥',
        };
    }

    public function isMobile(): bool
    {
        return in_array($this, [
            self::IOS,
            self::ANDROID,
            self::SYMBIAN,
            self::BLACKBERRY,
            self::WINDOWS_PHONE,
            self::FIRE_OS,
        ]);
    }

    public function isDesktop(): bool
    {
        return in_array($this, [
            self::WINDOWS,
            self::MACOS,
            self::LINUX,
            self::CHROMEOS,
            self::UNIX,
            self::BSD,
        ]);
    }

    public static function getAllValues(): array
    {
        return [
            self::WINDOWS->value => self::WINDOWS->label(),
            self::IOS->value => self::IOS->label(),
            self::ANDROID->value => self::ANDROID->label(),
            self::LINUX->value => self::LINUX->label(),
            self::MACOS->value => self::MACOS->label(),
            self::CHROMEOS->value => self::CHROMEOS->label(),
            self::UNIX->value => self::UNIX->label(),
            self::BSD->value => self::BSD->label(),
            self::SYMBIAN->value => self::SYMBIAN->label(),
            self::BLACKBERRY->value => self::BLACKBERRY->label(),
            self::WINDOWS_PHONE->value => self::WINDOWS_PHONE->label(),
            self::FIRE_OS->value => self::FIRE_OS->label(),
            self::UNKNOWN->value => self::UNKNOWN->label(),
        ];
    }
}