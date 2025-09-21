<?php

namespace App\Constants;

enum CommentStatus: int
{
    case HIDDEN = 0;           // 已隐藏
    case PENDING = 1;          // 待审核
    case APPROVED = 99;        // 审核通过

    public function label(): string
    {
        return match($this) {
            self::HIDDEN => '已隐藏',
            self::PENDING => '待审核',
            self::APPROVED => '审核通过',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::HIDDEN => 'Hidden',
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
        };
    }

    public function statusClass(): string
    {
        return match($this) {
            self::HIDDEN => 'status-hidden',
            self::PENDING => 'status-pending',
            self::APPROVED => 'status-approved',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::APPROVED;
    }

    public function isHidden(): bool
    {
        return $this === self::HIDDEN;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public static function getVisibleStatuses(): array
    {
        return [
            self::APPROVED->value
        ];
    }

    public static function getAllValues(): array
    {
        return [
            self::HIDDEN->value => self::HIDDEN->label(),
            self::PENDING->value => self::PENDING->label(),
            self::APPROVED->value => self::APPROVED->label(),
        ];
    }
}