<?php

namespace App\Helpers;

/**
 * HTML 辅助类
 * 提供 HTML 元素生成和处理的工具方法
 */
class StringHelper
{
    /**
     * 解析ID参数(支持逗号分隔的多个ID)
     */
    public static function parseIdsParam(string $param): array
    {
        if (empty($param)) {
            return [];
        }

        $ids = explode(',', $param);
        $ids = array_map('trim', $ids);
        $ids = array_filter($ids, 'is_numeric');
        $ids = array_map('intval', $ids);

        return array_unique($ids);
    }


}