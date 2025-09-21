<?php

namespace App\Constants;

enum ContentStatus: int
{
    case PROCESSING = 21;      // 处理中
    case READY = 29;           // 准备就绪
    case PUBLISHED = 31;       // 已发布
    case FEATURED = 39;        // 精选内容
    case ARCHIVED = 91;        // 已归档
    case COMPLETED = 99;       // 已完成

    public function label(): string
    {
        return match($this) {
            self::PROCESSING => '处理中',
            self::READY => '准备就绪',
            self::PUBLISHED => '已发布',
            self::FEATURED => '精选内容',
            self::ARCHIVED => '已归档',
            self::COMPLETED => '已完成',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::PROCESSING => 'Processing',
            self::READY => 'Ready',
            self::PUBLISHED => 'Published',
            self::FEATURED => 'Featured',
            self::ARCHIVED => 'Archived',
            self::COMPLETED => 'Completed',
        };
    }

    public function statusClass(): string
    {
        return match($this) {
            self::PROCESSING => 'status-processing',
            self::READY => 'status-ready',
            self::PUBLISHED => 'status-published',
            self::FEATURED => 'status-featured',
            self::ARCHIVED => 'status-archived',
            self::COMPLETED => 'status-completed',
        };
    }

    public function isVisible(): bool
    {
        return in_array($this, [
            self::PROCESSING,
            self::READY,
            self::PUBLISHED,
            self::FEATURED,
            self::ARCHIVED,
            self::COMPLETED
        ]);
    }

    public function isPublished(): bool
    {
        return in_array($this, [
            self::PUBLISHED,
            self::FEATURED,
            self::COMPLETED
        ]);
    }

    public static function getVisibleStatuses(): array
    {
        return [
            self::PROCESSING->value,
            self::READY->value,
            self::PUBLISHED->value,
            self::FEATURED->value,
            self::ARCHIVED->value,
            self::COMPLETED->value
        ];
    }

    public static function getPublishedStatuses(): array
    {
        return [
            self::PUBLISHED->value,
            self::FEATURED->value,
            self::COMPLETED->value
        ];
    }

    public static function getAllValues(): array
    {
        return [
            self::PROCESSING->value => self::PROCESSING->label(),
            self::READY->value => self::READY->label(),
            self::PUBLISHED->value => self::PUBLISHED->label(),
            self::FEATURED->value => self::FEATURED->label(),
            self::ARCHIVED->value => self::ARCHIVED->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
        ];
    }
}