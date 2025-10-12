<?php

namespace App\Core;

/**
 * I18n 国际化管理类
 *
 * 负责管理应用的多语言支持
 * - 支持中文(zh)和英文(en)
 * - 从URL参数读取语言设置
 * - 提供语言检测和切换功能
 */
class I18n
{
    private static ?string $currentLang = null;
    private const DEFAULT_LANG = 'zh'; // 默认中文
    private const SUPPORTED_LANGS = ['zh', 'en'];

    /**
     * 从请求中获取当前语言
     * 优先级: URL参数 > 默认语言
     *
     * @return string 当前语言代码 (zh 或 en)
     */
    public static function getCurrentLang(): string
    {
        if (self::$currentLang === null) {
            // 优先从URL参数获取
            $lang = $_GET['lang'] ?? null;

            // 验证语言是否支持
            if (!in_array($lang, self::SUPPORTED_LANGS, true)) {
                $lang = self::DEFAULT_LANG;
            }

            self::$currentLang = $lang;
        }

        return self::$currentLang;
    }

    /**
     * 设置当前语言
     *
     * @param string $lang 语言代码
     * @return void
     */
    public static function setCurrentLang(string $lang): void
    {
        if (in_array($lang, self::SUPPORTED_LANGS, true)) {
            self::$currentLang = $lang;
        }
    }

    /**
     * 获取支持的语言列表
     *
     * @return array 支持的语言代码数组
     */
    public static function getSupportedLangs(): array
    {
        return self::SUPPORTED_LANGS;
    }

    /**
     * 检查是否是中文
     *
     * @return bool
     */
    public static function isChinese(): bool
    {
        return self::getCurrentLang() === 'zh';
    }

    /**
     * 检查是否是英文
     *
     * @return bool
     */
    public static function isEnglish(): bool
    {
        return self::getCurrentLang() === 'en';
    }

    /**
     * 获取语言显示名称
     *
     * @param string|null $lang 语言代码,为null时使用当前语言
     * @return string 语言显示名称
     */
    public static function getLangDisplayName(?string $lang = null): string
    {
        $lang = $lang ?? self::getCurrentLang();

        return match($lang) {
            'zh' => '简体中文',
            'en' => 'English',
            default => '简体中文'
        };
    }

    /**
     * 获取语言短代码(用于UI显示)
     *
     * @param string|null $lang 语言代码,为null时使用当前语言
     * @return string 语言短代码 (CN/EN)
     */
    public static function getLangShortCode(?string $lang = null): string
    {
        $lang = $lang ?? self::getCurrentLang();

        return match($lang) {
            'zh' => 'CN',
            'en' => 'EN',
            default => 'CN'
        };
    }
}
