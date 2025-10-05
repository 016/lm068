<?php

namespace App\Constants;

enum VideoLinkStatus: int
{
    case INVALID = 0;      // 失效
    case VALID = 1;        // 正常

    public function label(): string
    {
        return match($this) {
            self::INVALID => '失效',
            self::VALID => '正常',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::INVALID => 'Invalid',
            self::VALID => 'Valid',
        };
    }

    public function isValid(): bool
    {
        return $this === self::VALID;
    }

    public function isInvalid(): bool
    {
        return $this === self::INVALID;
    }

    public static function fromBoolean(bool $valid): self
    {
        return $valid ? self::VALID : self::INVALID;
    }

    public static function getAllValues(): array
    {
        return [
            self::VALID->value => self::VALID->label(),
            self::INVALID->value => self::INVALID->label(),
        ];
    }
}