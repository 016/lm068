<?php

namespace App\Helpers;

/**
 * URL 辅助类
 * 提供 URL 元素生成和处理的工具方法
 */
class UrlHelper
{
    /**
     * 生成规范化的 URI（用于 SEO canonical URL）
     * 支持复杂的 URL 结构，包括中文、特殊字符、数组参数
     *
     * @param string $action 当前 action 的名称，如 "content/1099/中文标题"
     * @param array $params URI 中的 GET 参数，键值对数组
     * @param bool $filterEmpty 是否过滤空值参数，默认 true
     * @return string 完整的 URI 路径
     */
    public static function generateUri(string $action, array $params = [], bool $filterEmpty = true): string
    {
        // 规范化 action，移除前导斜杠
        $action = ltrim($action, '/');

        //remove s output
        unset($params['s']);

        // 过滤空值参数（可选）
        if ($filterEmpty) {
            $filteredParams = array_filter($params, function($value, $key) {
                // 保留 0 和 '0'，但过滤空字符串、null、空数组
                if (is_string($value)) {
                    return trim($value) !== '';
                }
                if (is_numeric($value)) {
                    return true;
                }
                return $value !== null && $value !== [] && $value !== false;
            }, ARRAY_FILTER_USE_BOTH);
        } else {
            $filteredParams = $params;
        }

        // 如果没有有效参数，直接返回 action
        if (empty($filteredParams)) {
            return '/' . $action;
        }

        // format url string
        $action = UrlHelper::formatString($action);

        // 构建查询字符串（自动处理 URL 编码）
        $queryString = http_build_query($filteredParams, '', '&', PHP_QUERY_RFC3986);

        // 组合完整 URI
        return '/' . $action . '?' . $queryString;
    }

    /**
     * for frontend generate canonical url , just remove $_GET['page'] out.
     * @param string $action
     * @param array $params
     * @param bool $filterEmpty
     * @return string
     */
    public static function generateCanonicalUrl(string $action, array $params = [], bool $filterEmpty = true): string
    {
        unset($params['page']);
        return self::generateUri($action, $params, $filterEmpty);
    }


    /**
     * format string in url to url friendly, like "a new book" => "a-new-book"
     * @param string $string
     * @return string
     */
    public static function formatString(string $string): string
    {
        $urlString = trim($string);
        // 将要保留的符号[+ . / ,]替换为连字符
        $urlString = preg_replace(['/\++/', '/\.+/', '/\/+/', '/,+/'], '-', $urlString);

        // 将要保留的符号$替换为USD
        $urlString = preg_replace(['/\$+/'], 'USD', $urlString);

        // 移除特殊字符，保留字母、数字、中文和连字符
        $urlString = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $urlString);
        // 将空格替换为连字符
        $urlString = preg_replace('/\s+/', '-', $urlString);

        // 移除多余的连字符
        $urlString = preg_replace('/-+/', '-', $urlString);
        // 限制长度（避免URL过长）
        $urlString = mb_substr($urlString, 0, 100);

        //CASE to case
        $urlString = strtolower($urlString);

        // encode url
        $urlString = rawurlencode($urlString);

        return $urlString;

    }
}