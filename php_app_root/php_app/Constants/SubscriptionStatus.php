<?php

namespace App\Constants;

enum SubscriptionStatus: int
{
    case UNSUBSCRIBED = 0;  // 取消订阅
    case SUBSCRIBED = 1;    // 已订阅

    public function label(): string
    {
        return match($this) {
            self::UNSUBSCRIBED => '取消订阅',
            self::SUBSCRIBED => '已订阅',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::SUBSCRIBED => 'Subscribed',
        };
    }

    public function isSubscribed(): bool
    {
        return $this === self::SUBSCRIBED;
    }

    public function isUnsubscribed(): bool
    {
        return $this === self::UNSUBSCRIBED;
    }

    public static function fromBoolean(bool $subscribed): self
    {
        return $subscribed ? self::SUBSCRIBED : self::UNSUBSCRIBED;
    }

    public static function getAllValues(): array
    {
        return [
            self::SUBSCRIBED->value => self::SUBSCRIBED->label(),
            self::UNSUBSCRIBED->value => self::UNSUBSCRIBED->label(),
        ];
    }
}