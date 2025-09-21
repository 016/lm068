<?php

namespace App\Constants;

enum VideoStatsStatus: int
{
    case FAILED = 0;           // 失败
    case NEW_TASK = 1;         // 新任务
    case IN_PROGRESS = 11;     // 进行中
    case COMPLETED = 99;       // 已完成

    public function label(): string
    {
        return match($this) {
            self::FAILED => '失败',
            self::NEW_TASK => '新任务',
            self::IN_PROGRESS => '进行中',
            self::COMPLETED => '已完成',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::FAILED => 'Failed',
            self::NEW_TASK => 'New Task',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    public function statusClass(): string
    {
        return match($this) {
            self::FAILED => 'status-failed',
            self::NEW_TASK => 'status-new',
            self::IN_PROGRESS => 'status-progress',
            self::COMPLETED => 'status-completed',
        };
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function isActive(): bool
    {
        return in_array($this, [self::NEW_TASK, self::IN_PROGRESS]);
    }

    public static function getAllValues(): array
    {
        return [
            self::FAILED->value => self::FAILED->label(),
            self::NEW_TASK->value => self::NEW_TASK->label(),
            self::IN_PROGRESS->value => self::IN_PROGRESS->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
        ];
    }
}