<?php

namespace App\Constants;

enum ContentType: int
{
    case ANNOUNCEMENT = 1;     // 网站公告
    case ARTICLE = 11;         // 一般文章
    case VIDEO = 21;           // 视频

    public function label(): string
    {
        return match($this) {
            self::ANNOUNCEMENT => '网站公告',
            self::ARTICLE => '一般文章',
            self::VIDEO => '视频',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::ANNOUNCEMENT => 'Announcement',
            self::ARTICLE => 'Article',
            self::VIDEO => 'Video',
        };
    }

    public function isVideoType(): bool
    {
        return $this === self::VIDEO;
    }

    public function isArticleType(): bool
    {
        return $this === self::ARTICLE;
    }

    public function isAnnouncementType(): bool
    {
        return $this === self::ANNOUNCEMENT;
    }

    public static function getAllValues(): array
    {
        return [
            self::ANNOUNCEMENT->value => self::ANNOUNCEMENT->label(),
            self::ARTICLE->value => self::ARTICLE->label(),
            self::VIDEO->value => self::VIDEO->label(),
        ];
    }
}