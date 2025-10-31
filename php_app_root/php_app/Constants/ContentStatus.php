<?php

namespace App\Constants;

enum ContentStatus: int
{
    case HIDDEN = 9;           // 隐藏
    case DRAFT = 11;            // 草稿
    case CREATIVE = 51;        // 创意
    case SCRIPT_START = 58;    // 脚本开
    case SCRIPT_DONE = 59;     // 脚本完
    case SHOOTING_START = 61;  // 开拍
    case SHOOTING_DONE = 69;   // 拍完
    case EDITING_START = 71;   // 开剪
    case EDITING_DONE = 79;    // 剪完
    case PENDING_PUBLISH = 91; // 待发布
    case PUBLISHED = 99;       // 已发布

    public function label(): string
    {
        return match($this) {
            self::HIDDEN => '隐藏',
            self::DRAFT => '草稿',
            self::CREATIVE => '创意',
            self::SCRIPT_START => '脚本开',
            self::SCRIPT_DONE => '脚本完',
            self::SHOOTING_START => '开拍',
            self::SHOOTING_DONE => '拍完',
            self::EDITING_START => '开剪',
            self::EDITING_DONE => '剪完',
            self::PENDING_PUBLISH => '待发布',
            self::PUBLISHED => '已发布',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::HIDDEN => 'Hidden',
            self::DRAFT => 'Draft',
            self::CREATIVE => 'Creative',
            self::SCRIPT_START => 'Script Start',
            self::SCRIPT_DONE => 'Script Done',
            self::SHOOTING_START => 'Shooting Start',
            self::SHOOTING_DONE => 'Shooting Done',
            self::EDITING_START => 'Editing Start',
            self::EDITING_DONE => 'Editing Done',
            self::PENDING_PUBLISH => 'Pending Publish',
            self::PUBLISHED => 'Published',
        };
    }

    public function statusClass(): string
    {
        return match($this) {
            self::HIDDEN => 'status-hidden',
            self::DRAFT => 'status-draft',
            self::CREATIVE => 'status-creative',
            self::SCRIPT_START => 'status-script-start',
            self::SCRIPT_DONE => 'status-script-done',
            self::SHOOTING_START => 'status-shooting-start',
            self::SHOOTING_DONE => 'status-shooting-done',
            self::EDITING_START => 'status-editing-start',
            self::EDITING_DONE => 'status-editing-done',
            self::PENDING_PUBLISH => 'status-pending-publish',
            self::PUBLISHED => 'status-published',
        };
    }

    public function bootstrapBadgeClass(): string
    {
        return match($this) {
            self::HIDDEN => 'text-bg-danger',         // 隐藏 - 红色(危险)
            self::DRAFT => 'text-bg-secondary',       // 草稿 - 灰色
            self::CREATIVE => 'text-bg-light',        // 创意 - 浅灰色
            self::SCRIPT_START => 'text-bg-info',     // 脚本开 - 蓝色(信息)
            self::SCRIPT_DONE => 'text-bg-primary',   // 脚本完 - 主色(蓝色)
            self::SHOOTING_START => 'text-bg-warning', // 开拍 - 黄色(警告)
            self::SHOOTING_DONE => 'text-bg-warning',  // 拍完 - 黄色(警告)
            self::EDITING_START => 'text-bg-warning',  // 开剪 - 黄色(进行中)
            self::EDITING_DONE => 'text-bg-info',      // 剪完 - 蓝色(接近完成)
            self::PENDING_PUBLISH => 'text-bg-warning', // 待发布 - 黄色(等待)
            self::PUBLISHED => 'text-bg-success',      // 已发布 - 绿色(成功)
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::HIDDEN => 'bi-eye-slash',
            self::DRAFT => 'bi-file-earmark',
            self::CREATIVE => 'bi-lightbulb',
            self::SCRIPT_START => 'bi-file-text',
            self::SCRIPT_DONE => 'bi-file-check',
            self::SHOOTING_START => 'bi-camera-video',
            self::SHOOTING_DONE => 'bi-camera-video-fill',
            self::EDITING_START => 'bi-scissors',
            self::EDITING_DONE => 'bi-check2-square',
            self::PENDING_PUBLISH => 'bi-clock',
            self::PUBLISHED => 'bi-check-circle',
        };
    }

    public function isVisible(): bool
    {
        return in_array($this, [
            self::SHOOTING_START,
            self::SHOOTING_DONE,
            self::EDITING_START,
            self::EDITING_DONE,
            self::PENDING_PUBLISH,
            self::PUBLISHED
        ]);
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function isInProduction(): bool
    {
        return in_array($this, [
            self::SCRIPT_START,
            self::SCRIPT_DONE,
            self::SHOOTING_START,
            self::SHOOTING_DONE,
            self::EDITING_START,
            self::EDITING_DONE
        ]);
    }

    public static function getVisibleStatuses(): array
    {
        return [
            self::SHOOTING_START->value,
            self::SHOOTING_DONE->value,
            self::EDITING_START->value,
            self::EDITING_DONE->value,
            self::PENDING_PUBLISH->value,
            self::PUBLISHED->value
        ];
    }

    public static function getPublishedStatuses(): array
    {
        return [
            self::PUBLISHED->value
        ];
    }

    public static function getProductionStatuses(): array
    {
        return [
            self::SCRIPT_START->value,
            self::SCRIPT_DONE->value,
            self::SHOOTING_START->value,
            self::SHOOTING_DONE->value,
            self::EDITING_START->value,
            self::EDITING_DONE->value
        ];
    }

    public static function getAllValues(): array
    {
        return [
            self::HIDDEN->value => self::HIDDEN->label(),
            self::DRAFT->value => self::DRAFT->label(),
            self::CREATIVE->value => self::CREATIVE->label(),
            self::SCRIPT_START->value => self::SCRIPT_START->label(),
            self::SCRIPT_DONE->value => self::SCRIPT_DONE->label(),
            self::SHOOTING_START->value => self::SHOOTING_START->label(),
            self::SHOOTING_DONE->value => self::SHOOTING_DONE->label(),
            self::EDITING_START->value => self::EDITING_START->label(),
            self::EDITING_DONE->value => self::EDITING_DONE->label(),
            self::PENDING_PUBLISH->value => self::PENDING_PUBLISH->label(),
            self::PUBLISHED->value => self::PUBLISHED->label(),
        ];
    }
}