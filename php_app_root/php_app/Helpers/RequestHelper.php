<?php

namespace App\Helpers;

use App\Constants\DeviceType;
use App\Constants\OsFamily;
use App\Constants\BrowserFamily;

/**
 * Array 辅助类
 * 提供 Array 元素生成和处理的工具方法
 */
class RequestHelper
{
    /**
     * 获取客户端真实 IP
     * @return string
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',  // Cloudflare
            'HTTP_X_REAL_IP',         // Nginx proxy
            'HTTP_X_FORWARDED_FOR',   // Standard proxy
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // 处理多个 IP 的情况（取第一个）
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // 验证 IP 格式
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * 解析 User Agent 获取设备信息
     * @param string $userAgent
     * @return array
     */
    public static function parseUserAgent(string $userAgent): array
    {
        $result = [
            'device_type' => DeviceType::DESKTOP->value,
            'os_family' => OsFamily::UNKNOWN->value,
            'browser_family' => BrowserFamily::UNKNOWN->value,
            'is_bot' => 0
        ];

        if (empty($userAgent)) {
            return $result;
        }

        $userAgentLower = strtolower($userAgent);

        // 1. 检测爬虫/机器人（优先检测）
        if (self::isBot($userAgentLower)) {
            $result['is_bot'] = 1;
            $result['device_type'] = DeviceType::BOT->value;
            return $result;
        }

        // 2. 检测操作系统
        $result['os_family'] = self::detectOS($userAgentLower);

        // 3. 检测设备类型
        $result['device_type'] = self::detectDeviceType($userAgentLower, $result['os_family']);

        // 4. 检测浏览器
        $result['browser_family'] = self::detectBrowser($userAgentLower);

        return $result;
    }

    /**
     * 检测是否为爬虫/机器人
     * @param string $userAgentLower
     * @return bool
     */
    public static function isBot(string $userAgentLower): bool
    {
        $botPatterns = [
            // 主流搜索引擎爬虫
            'googlebot',
            'bingbot',
            'slurp',              // Yahoo
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'sogou',

            // 社交媒体爬虫
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'pinterest',
            'vkshare',
            'whatsapp',
            'telegrambot',

            // SEO 工具爬虫
            'rogerbot',           // Moz
            'ahrefsbot',
            'semrushbot',
            'dotbot',
            'mj12bot',            // Majestic

            // 内容聚合/预览
            'embedly',
            'quora link preview',
            'showyoubot',
            'outbrain',
            'flipboard',

            // 通讯工具
            'slackbot',
            'discordbot',

            // 其他
            'applebot',
            'w3c_validator',
            'headlesschrome',
            'phantomjs',
            'selenium',

            // 通用特征词（放最后，避免误判）
            'bot',
            'crawler',
            'spider',
            'scraper',
            'scrapy',
            'curl',
            'wget',
            'python-requests',
            'go-http-client',
            'java/',
            'okhttp',
        ];

        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检测操作系统
     * @param string $userAgentLower
     * @return int
     */
    public static function detectOS(string $userAgentLower): int
    {
        if (str_contains($userAgentLower, 'windows phone')) {
            return OsFamily::WINDOWS_PHONE->value;
        }
        if (str_contains($userAgentLower, 'windows')) {
            return OsFamily::WINDOWS->value;
        }
        if (str_contains($userAgentLower, 'iphone') ||
            str_contains($userAgentLower, 'ipad') ||
            str_contains($userAgentLower, 'ipod')) {
            return OsFamily::IOS->value;
        }
        if (str_contains($userAgentLower, 'android')) {
            return OsFamily::ANDROID->value;
        }
        if (str_contains($userAgentLower, 'macintosh') ||
            str_contains($userAgentLower, 'mac os x')) {
            return OsFamily::MACOS->value;
        }
        if (str_contains($userAgentLower, 'cros')) {
            return OsFamily::CHROMEOS->value;
        }
        if (str_contains($userAgentLower, 'blackberry') ||
            str_contains($userAgentLower, 'bb10')) {
            return OsFamily::BLACKBERRY->value;
        }
        if (str_contains($userAgentLower, 'symbian') ||
            str_contains($userAgentLower, 'symbos')) {
            return OsFamily::SYMBIAN->value;
        }
        if (str_contains($userAgentLower, 'kindle') ||
            str_contains($userAgentLower, 'silk')) {
            return OsFamily::FIRE_OS->value;
        }
        if (preg_match('/(freebsd|openbsd|netbsd)/i', $userAgentLower)) {
            return OsFamily::BSD->value;
        }
        if (str_contains($userAgentLower, 'linux') ||
            str_contains($userAgentLower, 'ubuntu')) {
            return OsFamily::LINUX->value;
        }
        if (str_contains($userAgentLower, 'unix')) {
            return OsFamily::UNIX->value;
        }

        return OsFamily::UNKNOWN->value;
    }

    /**
     * 检测设备类型
     * @param string $userAgentLower
     * @param int $osFamily
     * @return int
     */
    public static function detectDeviceType(string $userAgentLower, int $osFamily): int
    {
        // 平板检测
        if (str_contains($userAgentLower, 'ipad') ||
            str_contains($userAgentLower, 'tablet') ||
            str_contains($userAgentLower, 'playbook') ||
            str_contains($userAgentLower, 'kindle') ||
            (str_contains($userAgentLower, 'android') &&
                !str_contains($userAgentLower, 'mobile'))) {
            return DeviceType::TABLET->value;
        }

        // 手机检测
        if (str_contains($userAgentLower, 'mobile') ||
            str_contains($userAgentLower, 'iphone') ||
            str_contains($userAgentLower, 'ipod') ||
            str_contains($userAgentLower, 'blackberry') ||
            str_contains($userAgentLower, 'windows phone') ||
            $osFamily === OsFamily::IOS->value ||
            $osFamily === OsFamily::ANDROID->value ||
            $osFamily === OsFamily::WINDOWS_PHONE->value) {
            return DeviceType::MOBILE->value;
        }

        return DeviceType::DESKTOP->value;
    }

    /**
     * 检测浏览器
     * @param string $userAgentLower
     * @return int
     */
    public static function detectBrowser(string $userAgentLower): int
    {
        // 微信/支付宝优先
        if (str_contains($userAgentLower, 'micromessenger')) {
            return BrowserFamily::WECHAT->value;
        }
        if (str_contains($userAgentLower, 'alipay')) {
            return BrowserFamily::ALIPAY->value;
        }

        // Edge 优先于 Chrome
        if (str_contains($userAgentLower, 'edg/') ||
            str_contains($userAgentLower, 'edge/')) {
            return BrowserFamily::EDGE->value;
        }

        // 国产浏览器
        if (str_contains($userAgentLower, '360')) {
            return BrowserFamily::BROWSER_360->value;
        }
        if (str_contains($userAgentLower, 'qqbrowser')) {
            return BrowserFamily::QQ_BROWSER->value;
        }
        if (str_contains($userAgentLower, 'ucbrowser') ||
            str_contains($userAgentLower, 'ubrowser')) {
            return BrowserFamily::UC_BROWSER->value;
        }
        if (str_contains($userAgentLower, 'sogou')) {
            return BrowserFamily::SOGOU->value;
        }

        // 其他浏览器
        if (str_contains($userAgentLower, 'brave')) {
            return BrowserFamily::BRAVE->value;
        }
        if (str_contains($userAgentLower, 'vivaldi')) {
            return BrowserFamily::VIVALDI->value;
        }
        if (str_contains($userAgentLower, 'yabrowser')) {
            return BrowserFamily::YANDEX->value;
        }
        if (str_contains($userAgentLower, 'opr/') ||
            str_contains($userAgentLower, 'opera')) {
            return BrowserFamily::OPERA->value;
        }
        if (str_contains($userAgentLower, 'samsungbrowser')) {
            return BrowserFamily::SAMSUNG_BROWSER->value;
        }
        if (str_contains($userAgentLower, 'firefox') ||
            str_contains($userAgentLower, 'fxios')) {
            return BrowserFamily::FIREFOX->value;
        }
        if (str_contains($userAgentLower, 'chrome') ||
            str_contains($userAgentLower, 'crios')) {
            return BrowserFamily::CHROME->value;
        }
        if (str_contains($userAgentLower, 'safari')) {
            return BrowserFamily::SAFARI->value;
        }
        if (str_contains($userAgentLower, 'msie') ||
            str_contains($userAgentLower, 'trident')) {
            return BrowserFamily::IE->value;
        }

        return BrowserFamily::UNKNOWN->value;
    }

}