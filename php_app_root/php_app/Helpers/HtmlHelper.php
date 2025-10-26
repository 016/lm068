<?php

namespace App\Helpers;

/**
 * HTML 辅助类
 * 提供 HTML 元素生成和处理的工具方法
 */
class HtmlHelper
{
    /**
     * 转义 HTML 特殊字符
     *
     * @param string|null $value 要转义的值
     * @param int $flags 转义标志
     * @param string $encoding 字符编码
     * @return string
     */
    public static function escape(?string $value, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): string
    {
        return htmlspecialchars($value ?? '', $flags, $encoding);
    }

    /**
     * 为 textarea 准备内容
     * 处理浏览器会自动删除 <textarea> 标签后第一个换行符的问题
     * 兼容不同操作系统的换行符：\r\n (Windows), \n (Unix/Linux), \r (旧Mac)
     *
     * @param string|null $content 原始内容
     * @param bool $escape 是否进行 HTML 转义（默认 true）
     * @return string
     */
    public static function prepareTextarea(?string $content, bool $escape = true): string
    {
        // 如果内容为空，直接返回空字符串
        if (empty($content)) {
            return '';
        }

        // 先进行 HTML 转义（如果需要）
        $processedContent = $escape ? self::escape($content) : $content;

        // 检查是否以换行符开头（兼容 \r\n, \n, \r）
        if (isset($processedContent[0])) {
            $firstChar = $processedContent[0];

            // 如果以任何形式的换行符开头，添加对应的换行符来补偿
            if ($firstChar === "\n" || $firstChar === "\r") {
                // 检测是 \r\n 还是单独的 \r 或 \n
                if ($firstChar === "\r" && isset($processedContent[1]) && $processedContent[1] === "\n") {
                    // Windows 风格 \r\n
                    $processedContent = "\r\n" . $processedContent;
                } else {
                    // 单独的 \n 或 \r
                    $processedContent = $firstChar . $processedContent;
                }
            }
        }

        return $processedContent;
    }

    /**
     * 规范化换行符（带日志）
     */
    public static function normalizeLineEndings(string $content, string $newline = "\n", bool $log = false): string
    {
        // 检测原始换行符类型
        $hasCRLF = strpos($content, "\r\n") !== false;
        $hasCR = strpos($content, "\r") !== false;
        $hasLF = strpos($content, "\n") !== false;

        if ($log && ($hasCRLF || $hasCR)) {
            $type = $hasCRLF ? 'CRLF' : ($hasCR ? 'CR' : 'LF');
            error_log("检测到换行符类型: {$type}, 长度: " . strlen($content));
        }

        // 规范化
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        if ($newline !== "\n") {
            $content = str_replace("\n", $newline, $content);
        }

        return $content;
    }
}