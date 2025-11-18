<?php

namespace App\Helpers;

/**
 * Markdown 辅助类
 * 提供 Markdown 元素生成和处理的工具方法
 */
class MarkdownHelper
{
    /**
     * 输入 Markdown 文本(```json contentString ```)，返回 Markdown 内部内容(contentString)，去除 Markdown 格式
     * @param $markdown
     * @param $language
     * @return string
     */
    public static function extractFromMarkdown($markdown, $language = null) {
        // 移除首尾空白
        $markdown = trim($markdown);
        $markdown = self::fixJsonNewlines($markdown);

        // 如果指定了语言类型,只匹配最外层特定语言的代码块
        if ($language !== null) {
            $pattern = '/^```' . preg_quote($language, '/') . '\s*\n?(.*)\n?```$/s';
            if (preg_match($pattern, $markdown, $matches)) {
                return trim($matches[1]);
            }
        }

        // 匹配最外层任意语言标记的代码块
        if (preg_match('/^```\w+\s*\n?(.*)\n?```$/s', $markdown, $matches)) {
            return trim($matches[1]);
        }

        // 匹配最外层普通代码块
        if (preg_match('/^```\s*\n?(.*)\n?```$/s', $markdown, $matches)) {
            return trim($matches[1]);
        }

        // 如果都不匹配,返回原始内容
        return $markdown;
    }

    public static function fixJsonNewlines($jsonString) {
        // 使用正则表达式匹配 JSON 中的字符串值
        // 匹配模式: "key": "value" 其中 value 可能包含未转义的 \n
        $result = preg_replace_callback(
            '/"([^"]+)":\s*"((?:[^"\\\\]|\\\\.)*)"/s',
            function($matches) {
                $key = $matches[1];
                $value = $matches[2];

                // 处理 value 中的未转义换行符
                // 先将已转义的 \\n 临时替换为占位符，避免重复转义
                $value = str_replace('\\n', "\x00ESCAPED_NEWLINE\x00", $value);

                // 将真实换行符替换为 \\n
                $value = str_replace("\n", '\\n', $value);

                // 恢复之前已转义的 \\n
                $value = str_replace("\x00ESCAPED_NEWLINE\x00", '\\n', $value);

                return '"' . $key . '": "' . $value . '"';
            },
            $jsonString
        );

        // 如果没有匹配到内容,返回原字符串
        return $result ?? $jsonString;
    }


    /**
     * add split (---) to all lv2 header(##), skip first lv2 header
     * @param $markdown
     * @return string
     */
    public static function addMarkdownHeaderSplit($markdown) {
        // 统一换行符为 \n，兼容 \r\n, \r, \n
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);

        // 按行分割
        $lines = explode("\n", $markdown);
        $result = [];
        $foundFirstH2 = false;

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // 检测是否为二级标题（## 开头，但不是 ### 或更多）
            if (preg_match('/^##\s+[^#]/', $line)) {
                if ($foundFirstH2) {
                    // 检查前面是否已经有分隔符（向上查找最近的非空行）
                    $hasSeparator = false;
                    for ($j = count($result) - 1; $j >= 0; $j--) {
                        $prevLine = trim($result[$j]);
                        if ($prevLine === '') {
                            continue; // 跳过空行
                        }
                        if ($prevLine === '---') {
                            $hasSeparator = true;
                        }
                        break; // 找到第一个非空行就停止
                    }

                    // 只有在没有分隔符时才添加
                    if (!$hasSeparator) {
                        // 如果上一行不是空行，先添加空行
                        if (!empty($result) && trim($result[count($result) - 1]) !== '') {
                            $result[] = '';
                        }
                        $result[] = '---';
                        $result[] = '';
                    }
                }
                $foundFirstH2 = true;
            }

            $result[] = $line;
        }

        return implode("\n", $result);
    }


    /**
     * 预处理 Markdown 内容，使之符合转换为 html 的前置标准
     * 处理块级元素前缺少空行的问题
     *
     * @param string $content Markdown 原始内容
     * @param array $options 可选配置项
     * @return string 处理后的 HTML
     */
    public static function prepareMarkdownForRender($content, $options = []): string
    {
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
        $content = "\n\n" . $content; // 6. 直接粗暴的添加空行在
//        var_dump(TextHelper::showRealString($content));
//        exit;

        return $content;
    }

    /**
     * 对 AI API 返回的结果进行入预处理
     *
     * rule 1.
     * - 如果输入的 Markdown 文本中第一个标题是三级标题(###)，
     *   则将文中所有的三级标题(###)替换为二级标题(##)。
     * - 否则，返回原始文本。
     * rule 2.
     *
     * rule 3.
     *
     * @param string $markdownText 输入的 Markdown 文本内容。
     * @return string 处理后的 Markdown 文本或原始文本。
     */
    public static function aiGenerateMarkdownFormat(string $markdownText): string
    {
        // 1. 使用正则表达式查找第一个标题（以'#'开头，后跟空格的行）
        //    '/^#+\s/m' 的解释:
        //    ^  - 匹配行的开头
        //    #+ - 匹配一个或多个'#'字符
        //    \s - 匹配一个空白字符
        //    m  - 多行模式，使'^'可以匹配每一行的开头
        if (preg_match('/^#+\s/m', $markdownText, $matches)) {

            // 2. 获取找到的第一个标题标记
            $firstHeadingTag = $matches[0];

            // 3. 判断第一个标题是否为三级标题 '### '
            //    使用 str_starts_with() 可以精确判断，避免将 '#### ' 误判
            if ($firstHeadingTag === '### ') {
                // 如果是，则将全文所有的 '### ' 替换为 '## '
                $markdownText = str_replace('### ', '## ', $markdownText);
            }
        }

        //2. \\n to \n for direct copy string !!! keep it at begin
        $markdownText = str_replace('\\n', "\n", $markdownText);

        //add --- to lv header ##
        $markdownText = self::addMarkdownHeaderSplit($markdownText);

        // 4. 如果没有找到标题，或者第一个标题不是三级标题，则直接返回原文
        return $markdownText;
    }
}