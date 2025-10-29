<?php

namespace App\Constants;

enum BrowserFamily: int
{
    case UNKNOWN = 0;
    case CHROME = 1;
    case SAFARI = 2;
    case FIREFOX = 3;
    case EDGE = 4;
    case IE = 5;
    case OPERA = 6;
    case SAMSUNG_BROWSER = 7;
    case UC_BROWSER = 8;
    case QQ_BROWSER = 9;
    case WECHAT = 10;
    case ALIPAY = 11;
    case BRAVE = 12;
    case VIVALDI = 13;
    case YANDEX = 14;
    case SOGOU = 15;
    case BROWSER_360 = 16;

    public function label(): string
    {
        return match($this) {
            self::UNKNOWN => '未知',
            self::CHROME => 'Chrome',
            self::SAFARI => 'Safari',
            self::FIREFOX => 'Firefox',
            self::EDGE => 'Edge',
            self::IE => 'IE',
            self::OPERA => 'Opera',
            self::SAMSUNG_BROWSER => '三星浏览器',
            self::UC_BROWSER => 'UC浏览器',
            self::QQ_BROWSER => 'QQ浏览器',
            self::WECHAT => '微信',
            self::ALIPAY => '支付宝',
            self::BRAVE => 'Brave',
            self::VIVALDI => 'Vivaldi',
            self::YANDEX => 'Yandex',
            self::SOGOU => '搜狗浏览器',
            self::BROWSER_360 => '360浏览器',
        };
    }

    public function englishLabel(): string
    {
        return match($this) {
            self::UNKNOWN => 'Unknown',
            self::CHROME => 'Chrome',
            self::SAFARI => 'Safari',
            self::FIREFOX => 'Firefox',
            self::EDGE => 'Edge',
            self::IE => 'Internet Explorer',
            self::OPERA => 'Opera',
            self::SAMSUNG_BROWSER => 'Samsung Browser',
            self::UC_BROWSER => 'UC Browser',
            self::QQ_BROWSER => 'QQ Browser',
            self::WECHAT => 'WeChat',
            self::ALIPAY => 'Alipay',
            self::BRAVE => 'Brave',
            self::VIVALDI => 'Vivaldi',
            self::YANDEX => 'Yandex',
            self::SOGOU => 'Sogou Explorer',
            self::BROWSER_360 => '360 Browser',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::UNKNOWN => '❓',
            self::CHROME => '🟢',
            self::SAFARI => '🧭',
            self::FIREFOX => '🦊',
            self::EDGE => '🔵',
            self::IE => '🔷',
            self::OPERA => '🔴',
            self::SAMSUNG_BROWSER => '📱',
            self::UC_BROWSER => '🌐',
            self::QQ_BROWSER => '🐧',
            self::WECHAT => '💬',
            self::ALIPAY => '💳',
            self::BRAVE => '🦁',
            self::VIVALDI => '🎵',
            self::YANDEX => '🟡',
            self::SOGOU => '🔍',
            self::BROWSER_360 => '🛡️',
        };
    }

    public function isWebView(): bool
    {
        return in_array($this, [
            self::WECHAT,
            self::ALIPAY,
        ]);
    }

    public function isChineseBrowser(): bool
    {
        return in_array($this, [
            self::UC_BROWSER,
            self::QQ_BROWSER,
            self::WECHAT,
            self::ALIPAY,
            self::SOGOU,
            self::BROWSER_360,
        ]);
    }

    public static function getAllValues(): array
    {
        return [
            self::CHROME->value => self::CHROME->label(),
            self::SAFARI->value => self::SAFARI->label(),
            self::FIREFOX->value => self::FIREFOX->label(),
            self::EDGE->value => self::EDGE->label(),
            self::IE->value => self::IE->label(),
            self::OPERA->value => self::OPERA->label(),
            self::SAMSUNG_BROWSER->value => self::SAMSUNG_BROWSER->label(),
            self::UC_BROWSER->value => self::UC_BROWSER->label(),
            self::QQ_BROWSER->value => self::QQ_BROWSER->label(),
            self::WECHAT->value => self::WECHAT->label(),
            self::ALIPAY->value => self::ALIPAY->label(),
            self::BRAVE->value => self::BRAVE->label(),
            self::VIVALDI->value => self::VIVALDI->label(),
            self::YANDEX->value => self::YANDEX->label(),
            self::SOGOU->value => self::SOGOU->label(),
            self::BROWSER_360->value => self::BROWSER_360->label(),
            self::UNKNOWN->value => self::UNKNOWN->label(),
        ];
    }
}