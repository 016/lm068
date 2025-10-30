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
    private const DEFAULT_LANG = 'zh'; // 默认中文
    private const SUPPORTED_LANGS = ['zh', 'en'];

    public static function initLang(): void{
        $requestUri = $_SERVER['REQUEST_URI'];

        $lang = self::DEFAULT_LANG; // 默认语言

        // 使用正则表达式从 URI 路径的开头匹配语言代码
        if (preg_match('#^/(' . implode('|', self::SUPPORTED_LANGS) . ')/#', $requestUri, $matches)) {
            $lang = $matches[1];

            // 可选：从 REQUEST_URI 中移除语言前缀，以便后续路由处理
            // 很多框架的路由器依赖于不含语言前缀的路径
            $_SERVER['REQUEST_URI'] = substr($requestUri, strlen($matches[0]) - 1);
        }

        // 定义一个全局常量或变量来存储当前语言
        define('CURRENT_LANG', $lang);

    }

    /**
     * 从请求中获取当前语言
     * 优先级: URL参数 > 默认语言
     *
     * @return string 当前语言代码 (zh 或 en)
     */
    public static function getCurrentLang(): string
    {
        if (defined('CURRENT_LANG')) {
            return CURRENT_LANG;
        }

        return self::DEFAULT_LANG;
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
            define('CURRENT_LANG', $lang);
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
