<?php

namespace App\Helpers;

/**
 * HTML 辅助类
 * 提供 HTML 元素生成和处理的工具方法
 */
class TextHelper
{
    /**
     * 显示隐藏字符
     *
     * @param string $inputString input orginal string for show
     * @param string $characters 格式化类型 "\x00..\x1f"  "\r\n\t\0"
     * @return string
     */
    public static function showRealString(string $inputString, string $characters = "\x00..\x1f"): string
    {
        return addcslashes($inputString, $characters);
    }

    /**
     * 将 Markdown 内容转换为 HTML
     * 处理块级元素前缺少空行的问题
     *
     * @param string $content Markdown 原始内容
     * @param array $options 可选配置项
     * @return string 处理后的 HTML
     */
    public static function renderMarkdown($content, $options = []) {
        // 空内容检查
        if (empty($content) || empty(trim($content))) {
            return '';
        }

        // 默认配置
        $defaults = [
            'add_prefix_newline' => true,   // 是否添加前置换行
            'normalize_newlines' => true,   // 是否规范化换行符
            'remove_trailing_spaces' => true, // 是否移除行尾空格
        ];

        $config = array_merge($defaults, $options);

        // 1. 规范化换行符（统一为 \n）
        if ($config['normalize_newlines']) {
            $content = str_replace(["\r\n", "\r"], "\n", $content);
        }

        // 2. 移除行尾空格
        if ($config['remove_trailing_spaces']) {
            $content = preg_replace('/[ \t]+$/m', '', $content);
        }

        // 3. 去除首尾空白
        $content = trim($content);

        // 4. 如果以块级元素开头，添加前置换行
        if ($config['add_prefix_newline']) {
            $blockPatterns = [
                '/^#{1,6}\s/',           // 标题 (# ## ###)
                '/^[-*+]\s/',            // 无序列表
                '/^\d+\.\s/',            // 有序列表
                '/^>\s/',                // 引用
                '/^```/',                // 代码块
                '/^~~~/',                // 代码块（波浪线）
                '/^---$/',               // 水平线
                '/^\|/',                 // 表格
            ];

            foreach ($blockPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $content = "\n" . $content;
                    break;
                }
            }
        }

        // 5. 确保块级元素之间有足够的空行
        $content = preg_replace('/([^\n])\n(#{1,6}\s)/', "$1\n\n$2", $content);

        return $content;
    }



}